<?php
/*
class crud extends dblyze {

    public $table;

    protected $db;

    public function __construct($table) {
        // Call database.
        global $db;
        $this -> db = $db;

        // Call parent constructor,
        parent::__construct();

        // Set table.
        $this -> table = $table;
    }

    public function get($config = []) {
        $defaults = [
            'item' => 0
        ];
        $config = array_merge($defaults, $config);

        if ($config['item'] > 0) {

        } else {
            $sql = $this -> get_list();
            echo " <br><br><br> $sql <br><br><br>";
            return $this -> db -> fetch_array($sql);
        }
    }

    public function get_list() {

        $rel_out = parent::relations([
            'TABLE_NAME' => $this -> table
        ]);
        $rel_in = parent::relations([
            'REFERENCED_TABLE_NAME' => $this -> table
        ]);

        $select_strings = [];
        $join_strings = [];
        var_dump($rel_out);

        foreach ($rel_out as $rel) {
            if ($rel['REFERENCED_TABLE_NAME'] !== null || $rel['REFERENCED_COLUMN_NAME'] !== null) {
                $select_strings[] = ", $rel[REFERENCED_TABLE_NAME].$rel[REFERENCED_COLUMN_NAME]
                                AS $rel[REFERENCED_TABLE_NAME]_$rel[REFERENCED_COLUMN_NAME] ";
                $join_strings[] = "JOIN $rel[REFERENCED_TABLE_NAME]
                                        ON $rel[TABLE_NAME].$rel[COLUMN_NAME] = $rel[REFERENCED_TABLE_NAME].$rel[REFERENCED_COLUMN_NAME] ";
            }
        }
        foreach ($rel_in as $rel) {
            if ($rel['TABLE_NAME'] !== null || $rel['COLUMN_NAME'] !== null) {
                $select_strings[] = ", $rel[TABLE_NAME].$rel[COLUMN_NAME]
                                AS $rel[TABLE_NAME]_$rel[COLUMN_NAME] ";
                $join_strings[] = "JOIN $rel[TABLE_NAME]
                                    ON $rel[REFERENCED_TABLE_NAME].$rel[REFERENCED_COLUMN_NAME] = $rel[TABLE_NAME].$rel[COLUMN_NAME]";
            }
        }

        $sql = "SELECT $this->table.* ";
        $sql .= implode(' ', $select_strings);
        $sql .= "FROM $this->table ";
        $sql .= implode(' ', $join_strings);

        return $sql;
    }

}*/

// LAV DET HER