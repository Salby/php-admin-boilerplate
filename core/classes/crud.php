<?php

class crud extends dblyze {

    public $table;

    protected $db;

    /**
     * crud constructor.
     *
     * @param string $table
     */
    public function __construct($table) {
        // Call database.
        global $db;
        $this -> db = $db;

        // Call parent constructor.
        parent::__construct();

        // Set table name.
        $this -> table = $table;
    }

    /**
     * ## Config
     *
     * **Array** *column_params*
     *
     * **Array** *exclude*
     *
     * @param array $config
     *
     * @return array
     */
    public function get($config = []) {
        $defaults = [
            'column_params' => [],
            'query_params' => [],
            'exclude' => []
        ];
        $config = array_merge($defaults, $config);

        // Get base table column info.
        $columns_base = parent::columns($this->table);

        // Get relations coming in.
        $rel_in = parent::relations([
            'REFERENCED_TABLE_NAME' => $this -> table
        ]);
        // Get column info from related tables (out).
        $tables_in = [];
        foreach ($rel_in as $rel) {
            if ($rel['TABLE_NAME'] !== $this->table && !in_array($rel['TABLE_NAME'], $config['exclude']))
                $tables_in[$rel['TABLE_NAME']] = parent::columns($rel['TABLE_NAME']);
        }

        // Build select strings from base columns.
        $query_select_base = [];
        foreach ($columns_base as $column) {
            $table = $this -> table;
            $column = $column['Field'];
            $query_select_base[] = $table.".".$column." AS ".$table."_".$column;
        }

        // Build select strings from related table columns (out).
        $query_select_in = $this -> query_select($tables_in);

        $select_str = implode(', ', [
            implode(', ', $query_select_base),
            implode(', ', $query_select_in)
        ]);
        $select_str = trim($select_str, ', ');

        $joins = [];
        foreach ($rel_in as $rel) {
            if (!in_array($rel['TABLE_NAME'], $config['exclude']))
                $joins[] = "JOIN ".$rel['TABLE_NAME'].
                    " ON ".$rel['REFERENCED_TABLE_NAME'].".".$rel['REFERENCED_COLUMN_NAME'].
                    " = ".$rel['TABLE_NAME'].".".$rel['COLUMN_NAME'];
        }

        $joins_str = implode(' ', $joins);

        $params = [];
        $query_params = [];
        if (!empty($config['column_params'])) {
            foreach ($config['column_params'] as $column => $value) {
                $params[] = $value;

                $column = substr_count($column, '.')
                    ? $column
                    : $this->table.".".$column;
                $query_params[] = $column." = ?";
            }
        }

        $params_str = !empty($query_params)
            ? "WHERE ".implode(' AND ', $query_params)
            : "";


        // Build SQL query.
        $sql = "SELECT ".$select_str." FROM ".$this->table." ".$joins_str." ".$params_str;

        if (!empty($config['query_params'])) {
            foreach ($config['query_params'] as $param => $value) {
                $sql .= " $param $value";
            }
        }

        var_dump(trim($sql));
        $result = $this -> db -> fetch_array(trim($sql), $params);
        /*if (count($result) > 1) {
            return $result;
        } else {
            return call_user_func_array('array_merge', $result);
        }*/
        return $result;
    }

    public function query_select($tables) {
        $column_selects = [];
        foreach ($tables as $table_name => $columns) {
            $table = $table_name;
            foreach ($columns as $column) {
                $column = $column['Field'];
                $column_selects[] = $table.".".$column." AS ".$table."_".$column;
            }
        }
        return $column_selects;
    }
}