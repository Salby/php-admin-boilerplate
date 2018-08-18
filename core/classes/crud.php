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
     * **Array** *parameters*
     *
     * **Array** *exclude*
     *
     * @param array $config
     */
    public function get($config = []) {
        $defaults = [
            'parameters' => [],
            'exclude' => []
        ];
        $config = array_merge($defaults, $config);

        // Get base table column info.
        $columns_base = parent::columns($this->table);

        // Get relations going out.
        $rel_out = parent::relations([
            'REFERENCED_TABLE_NAME' => $this -> table
        ]);
        // Get column info from related tables (out).
        $tables_out = [];
        foreach ($rel_out as $rel) {
            if ($rel['TABLE_NAME'] !== $this->table && !in_array($rel['TABLE_NAME'], $config['exclude']))
                $tables_out[$rel['TABLE_NAME']] = parent::columns($rel['TABLE_NAME']);
        }

        // Build select strings from base columns.
        $query_select_base = [];
        foreach ($columns_base as $column) {
            $table = $this -> table;
            $column = $column['Field'];
            $query_select_base[] = $table.".".$column." AS ".$table."_".$column;
        }

        // Build select strings from related table columns (out).
        $query_select_out = $this -> query_select($tables_out);

        $select_str = implode(', ', [
            implode(', ', $query_select_base),
            implode(', ', $query_select_out)
        ]);

        $params = [];
        $query_params = [];
        if (!empty($config['parameters'])) {
            foreach ($config['parameters'] as $column => $value) {
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
        $sql = "SELECT ".$select_str." FROM ".$this->table." ".$params_str;
        $result = $this -> db -> fetch_array($sql, $params);
        if (count($result) > 1) {
            return $result;
        } else {
            return call_user_func_array('array_merge', $result);
        }
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