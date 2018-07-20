<?php

class table_builder {

    public $card = false;
    public $table_name;
    
    protected $db;

    public function __construct($table_name) {
        global $db;
        $this -> db = $db;

        $this -> table_name = $table_name;
    }

    /**
     * Analyzes table.
     *
     * @param string table_name - Table to analyze.
     *
     * @return array $table_data
     */
    public function analyze_table() {
        $sql = "SHOW COLUMNS FROM ".$this->table_name;
        return $this -> db -> fetch_array($sql);
    }

    /**
     * @param string table_name
     *
     * @return array table_data
     */
    public function get_data() {

        $sql = "SELECT *
                  FROM $this->table_name
                WHERE
                  deleted = 0";
        $table_data = $this -> db -> fetch_array($sql);

        return $table_data;
    }

    /**
     * Builds table.
     *
     * @param array $labels
     * @param array $exceptions
     * @param array $config
     */
    public function build(
        $labels = array(),
        $exceptions = array(),
        $config = array()
    ) {
        $data = $this -> analyze_table();

        if ($this -> card) {

            $contextual_id = $this -> table_name.'_contextual';
            $title = array_key_exists('table_title', $labels) // Check if table title replacement exists.
                ? $labels['table__title'] // Replacement string.
                : ucfirst($this->table_name); // Capitalize table name.
            
            // Delete action.
            $on_click = "Dialog.create(
                'Delete',
                'Are you sure you want to delete this?
                'TableActions.delete(\'$this->table_name.php?mode=delete\', \'$this->table_name\')'
            );";

            echo "<div class='card'>
            <div class='card__header'>
                <ul class='header__row'>
                    <li><ul class='header__row-list--left'>
                        <li><h1>$title</h1></li>
                    </ul></li>
                </ul>
                <ul class='header__row--contextual' id='$contextual_id'>
                    <li><ul class='header__row-list--left'>
                        <li class='contextualAmount'></li>
                    </ul></li>
                    <li><ul class='header__row-list--right'>
                        <li><button onclick='$on_click'̣ class='button__icon--dark'>
                            <i class='material-icons'>delete</i>
                        </button></li>
                    </ul></li>
                </ul>
            </div>";
        }

        $head = $this -> build_head($data, $labels, $exceptions);
        $body = $this -> build_body($exceptions);

        if ($this -> card && !empty($contextual_id)) {
            echo "<table class='table' id='$this->table_name' data-contextual='$contextual_id'>";
        } else {
            echo "<table class='table' id='$this->table_name'>";
        }
        echo $head;
        echo $body;
        echo "</table>";

        if ($this -> card) {
            echo "</div>";
        }
    }

    /**
     * Builds table head from data.
     *
     * @param $name
     * @param $row
     * @param array $labels
     * @param array $exceptions
     * @return string table_head
     */
    public function build_head(
        $row,
        $labels = array(),
        $exceptions = array()
    ) {
        $table_head = "<thead><tr>";

        $check_name = $this->table_name . '_select';
        $table_head .= "<th class='select'><input type='checkbox' name='$check_name' class='table__checkbox master'></th>";

        foreach ($row as $col) {

            if (!array_key_exists($col['Field'], $exceptions)) {

                $name = array_key_exists($col['Field'], $labels)
                    ? $labels[$col['Field']]
                    : ucfirst($col['Field']);
                
                if ($col['Type'] === 'int(11)' || $col['Type'] === 'tinyint(1)') {
                    echo "Is int";
                    $table_head .= "<th class='type--align-right'>$name</th>";
                }

                else {
                    $table_head .= "<th>$name</th>";
                }

            }
            
        }
        
        $table_head .= "</tr></thead>";

        return $table_head;
    }

    /**
     * Builds table body from data.
     *
     * @param array $exceptions
     * @param array $columns
     * @param array $config
     *
     * @return string $table_body
     */
    public function build_body(
        $exceptions = array(),
        $columns = array(),
        $config = array('deleted' => 0)
    ) {

        $data = $this -> get_data();

        $table_body = "<tbody>";

        debug($data);

        for ($i = 0; $i < count($data); $i++) {
            $row = $data[$i];

            $check_name = $this->table_name . '_select';
            $table_row = "<tr>
                <td class='select'><input type='checkbox' class='table__checkbox' value='$row[id]' name='$check_name'></td>";

            foreach ($row as $cell => $val) {
                if (array_key_exists($cell, $exceptions)) {

                }
                else if (is_int($val)) {
                    $table_row .= "<td class='type--align-right'>$val</td>";
                }
                else if (is_string($val)) {
                    $table_row .= "<td>$val</td>";
                }
            }

            $table_row .= "</tr>";

            $table_body .= $table_row;
        }

        $table_body .= "</tbody>";

        return $table_body;
    }
}

function debug($arr) {
    foreach ($arr as $val) {
        var_dump($val);
        echo "<br><br>";
    }
    echo "<br><br>";
}