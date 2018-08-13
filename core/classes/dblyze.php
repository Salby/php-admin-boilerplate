<?php

class dblyze {

    protected $db;

    public function __construct() {
        // Call database.
        global $db;
        $this->db = $db;
    }

    /**
     * Returns column data from table.
     *
     * ## Example
     * ```
     * $dblyze = new dblyze();
     *
     * $dblyze -> columns('some_table');
     * ```
     * -- Will return column information from __*some_table*__.
     *
     * @param string $table_name - Table to analyze.
     *
     * @return array
     */
    public function columns($table_name) {
        $sql = "SHOW FULL COLUMNS FROM $table_name";
        return $this -> db -> fetch_array($sql);
    }

    /**
     * # Relations
     *
     * Finds relational information from parameters given:
     *
     * ## Parameters
     *
     * __String__ _TABLE_NAME_
     *
     * __String__ _COLUMN_NAME_
     *
     * __String__ _CONSTRAINT_NAME_
     *
     * __String__ _REFERENCED_TABLE_NAME_
     *
     * __String__ _REFERENCED_COLUMN_NAME_
     *
     * ## Example
     * ```
     * $dblyze = new dblyze();
     *
     * $dblyze -> relations([
     *      'TABLE_NAME' => 'some_table',
     *      'COLUMN_NAME' => 'some_column'
     * ]);
     * ```
     * -- Returns relational information where the table name is __*some_table*__ and the column name is __*some_column*__.
     *
     * @param $parameters
     *
     * @return array
     */
    public function relations($parameters) {
        $params = [];
        $sql = "SELECT TABLE_NAME,
                  COLUMN_NAME,
                  CONSTRAINT_NAME,
                  REFERENCED_TABLE_NAME,
                  REFERENCED_COLUMN_NAME
                  FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE
                  TABLE_SCHEMA = SCHEMA()";

        foreach ($parameters as $requirement => $value) {
            $params[] = ''.$value.'';
            $sql .= "AND $requirement = ?";
        }

        return $this -> db -> fetch_array($sql, $params);
    }

    /**
     * # Relational table
     *
     * Finds table column info from outer relation via a relational table.
     *
     * @param string $base_table
     * @param string $relational_table
     *
     * @return array
     */
    public function relational_table($base_table, $relational_table) {
        $columns = [];

        // Get relational table relations (2).
        $table_rel_out = $this -> relations([
            'TABLE_NAME' => $relational_table
        ]);

        // Exclude columns that relate to our primary key.
        $ignore = [];
        foreach ($table_rel_out as $column) {
            if ($column['REFERENCED_TABLE_NAME'] === $base_table)
                $ignore[] = $column['COLUMN_NAME'];
        }

        // Build fake columns from relation end-point column (starting from base table).
        foreach ($table_rel_out as $column) {
            if ($column['REFERENCED_TABLE_NAME'] !== $base_table) {
                // Build fake column.
                $fake_column = [
                    'Field' => $column['REFERENCED_TABLE_NAME'],
                    'Type' => null,
                    'Null' => 'NO',
                    'Key' => 'MTM',
                    'Default' => null,
                    'Extra' => '',
                    'Columns' => [] // Insert column(s) here.
                ];
                // Find columns in relational table.
                $table_rel_info = $this -> columns($relational_table);
                // Insert columns into $fake_column['Columns'] if not ignored.
                foreach ($table_rel_info as $rel_column) {
                    if (!in_array($rel_column['Field'], $ignore))
                        $fake_column['Columns'][] = $rel_column;
                }
                $columns[] = $fake_column;
            }
        }

        // Return fake columns.
        return $columns;
    }

    /**
     * # Table info
     *
     * Finds column information based on table name.
     *
     * @param string $table_name
     *
     * @return array
     */
    public function table_info($table_name) {
        // Get column information for base table.
        $table_info = $this -> columns($table_name);

        // Find columns that relate to a column in base table.
        $table_rel_in = $this -> relations([
            'REFERENCED_TABLE_NAME' => $table_name
        ]);
        if (!empty($table_rel_in)) {
            foreach ($table_rel_in as $column) {
                $foreign_table = $column['TABLE_NAME'];
                $foreign_table_info = $this -> columns($foreign_table);

                // Count primary keys in table.
                $pri = [];
                foreach ($foreign_table_info as $check) {
                    if ($check['Key'] == 'PRI')
                        $pri[] = $check;
                }

                // If there are no primary keys, foreign table is a relational table.
                if (!count($pri)) { // Is relational table.
                    // Get fake columns from relational table.
                    $fake_columns = $this -> relational_table($table_name, $foreign_table);
                    // Iterate and insert fake columns as normal columns.
                    foreach ($fake_columns as $fk) {
                        $table_info[] = $fk;
                    }
                } else { // Isn't relational table.
                    foreach ($foreign_table_info as $foreign_column) {
                        // Iterate and insert foreign columns if they aren't primary or foreign keys.
                        if ($foreign_column['Key'] != 'PRI' && $foreign_column['Key'] != 'MUL')
                            $table_info[] = $foreign_column;
                    }
                }
            }
        }

        // Return all related table info.
        return $table_info;
    }

}