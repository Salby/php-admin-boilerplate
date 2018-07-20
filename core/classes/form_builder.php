<?php

class form_builder {

    public $regex = array(
        'numbers_only' => '/^\d+$/'
    );

    protected $db;

    public function __construct() {
        global $db;
        $this -> db = $db;
    }

    /**
     * Analyze table
     *
     * @param $table_name
     * @param $standalone
     *
     * @return array $table_data
     */
    public function analyze_table($table_name, $standalone = false) {
        $sql = "SHOW FULL COLUMNS FROM " . $table_name; // Get columns from $table_name
        $table_data = $this -> db -> fetch_array($sql); // Save in $table_data

        if (!$standalone) {
            $sql = "SELECT TABLE_NAME,
                      COLUMN_NAME,
                      CONSTRAINT_NAME,
                      REFERENCED_TABLE_NAME,
                      REFERENCED_COLUMN_NAME
                      FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                    WHERE
                      REFERENCED_TABLE_NAME = '$table_name'";
            $table_rel_data = $this->db->fetch_array($sql);

            if (!empty($table_rel_data)) {
                foreach ($table_rel_data as $row) {
                    $table_rel_name = $row['TABLE_NAME'];
                    $sql = "SHOW FULL COLUMNS FROM " . $table_rel_name;
                    $res = $this->db->fetch_array($sql);

                    $pri = [];
                    foreach ($res as $col) {
                        if ($col['Key'] == 'PRI') {
                            $pri[] = $col;
                        }
                    }
                    if (!count($pri)) { // Is relational table.

                        // Get table relations.
                        $params = [''.$table_rel_name.''];
                        $sql = "SELECT TABLE_NAME,
                                  COLUMN_NAME,
                                  CONSTRAINT_NAME,
                                  REFERENCED_TABLE_NAME,
                                  REFERENCED_COLUMN_NAME
                                  FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                                WHERE
                                  TABLE_NAME = ?";
                        $relations = $this -> db -> fetch_array($sql, $params);

                        // Exclude columns that relate to our primary key.
                        $not_to_show = array();
                        foreach ($relations as $rel) {
                            if ($rel['REFERENCED_TABLE_NAME'] === $table_name) { // If column relates to our primary key.
                                $not_to_show[] = $rel['COLUMN_NAME']; // Add column_name to not_to_show array.
                            }
                        }

                        foreach ($relations as $rel) {
                            if ($rel['REFERENCED_TABLE_NAME'] !== $table_name) { // Make sure the referenced table isn't our table.

                                // Build new column array like the others, but with a special Key.
                                $column = array(
                                    'Field' => $rel['REFERENCED_TABLE_NAME'], // Set referenced_table_name as the Field ( This is useful later because we don't have to check where the table relates to ).
                                    'Type' => null,
                                    'Null' => 'NO',
                                    'Key' => 'MTM', // `Many-to-many`
                                    'Default' => null,
                                    'Extra' => '',
                                    'Columns' => array() // Insert column here.
                                );

                                // Find columns in relation table
                                $sql = "SHOW FULL COLUMNS FROM ".$table_rel_name;
                                $rel_columns = $this -> db -> fetch_array($sql);

                                foreach ($rel_columns as $rel_column) {
                                    if (!in_array($rel_column['Field'], $not_to_show)) { // Check if the column should be shown.
                                        $column['Columns'][] = $rel_column; // Add the column to our columns array.
                                    }
                                }

                                $table_data[] = $column; // Insert the column into table_data.
                            }
                        }
                    } else { // Isn't relational table.
                        foreach ($res as $col) {
                            if ($col['Key'] != 'PRI' && $col['Key'] != 'MUL') { // You don't want to add any columns with keys from this table.
                                $table_data[] = $col; // Add column to $table_data if it isn't a primary or foreign key.
                            }
                        }
                    }
                }
            }
        }

        return $table_data;
    }

    /**
     * Build form from MySQL table columns.
     *
     * @param array $conf
     * @param array $labels
     * @param array $exceptions
     */
    public function build(
        $conf = array(),
        $labels = array(),
        $exceptions = array()
    ) {
        $standalone = isset($conf['standalone'])
            ? $conf['standalone']
            : false;
        $table_data = $this -> analyze_table($conf['table_name'], $standalone); // Get columns from table

        echo "<form class='form__main' id='$conf[table_name]' action='$conf[action]' method='$conf[method]' novalidate enctype='multipart/form-data'>";

        if (!empty($labels['_form_title'])) {
            $subTitle = !empty($labels['_form_subtitle'])
                ? $labels['_form_subtitle']
                : '';
            echo $this -> title($labels['_form_title'], $subTitle);
        }

        foreach ($table_data as $col) {

            if ($col['Key'] !== 'PRI') { // Make sure column isn't a primary key.

                $input = new Input();

                $input -> label = array_key_exists($col['Field'], $labels) // Check if label exists.
                    ? $labels[$col['Field']] // Replace label name.
                    : ucfirst($col['Field']); // Use name from table column.
                
                $input -> required = $col['Null'] === 'NO' // Required bool from column Null.
                    ? 'required' // Required.
                    : ''; // Not required.

                $regex = $this -> regex; // Get regex from class.
                
                $input -> id = $conf['table_name'] . '_' . $col['Field']; // Build identifier.

                if (array_key_exists($col['Field'], $exceptions)) { // Check if field is in exceptions.

                    if (!empty($exceptions[$col['Field']])) {
                        
                        $field = $exceptions[$col['Field']];

                        if (strpos($field, '___id___')) { // If id placeholder exists.
                            $field = str_replace('___id___', $input->id, $field); // Replace with id.
                        }
                        if (strpos($field, '___label___')) { // If label placeholder exists.
                            $field = str_replace('___label___', $input->label, $field); // Replace with label.
                        }
                        if (strpos($field, '___required___')) { // If required placeholder exists.
                            $field = str_replace('___required___', $input->required, $field); // Replace with required status.
                        }

                        echo $field; // Insert field.

                    }
                
                } else {

                    if ($col['Key'] === 'MTM') { // Is many-to-many relation.

                        $foreign_table = $col['Field'];
                        $columns = $col['Columns'];
                        $title = ucfirst($foreign_table);

                        $className = count($columns) === 1
                            ? 'form__group-items'
                            : 'form__group-items--rows';

                        echo "<div class='$className form_items'>
                            <div class='title'>$title</div>
                            <div class='form_items_model'>";

                            foreach ($columns as $c) {

                                if ($c['Key'] !== 'PRI') {

                                    $field = $foreign_table . '_' . $c['Field'];
                                    $input -> label = array_key_exists($field, $labels)
                                        ? $labels[$field]
                                        : ucfirst($field);
                                    $input -> id = $conf['table_name']. '_' . $c['Field'];
                                    $input -> name = $input -> id . '[]';
                                    $input -> contained = false;

                                    if ($c['Key'] === 'MUL') {

                                        // Get foreign values.
                                        $sql = "SELECT *
                                              FROM $foreign_table
                                            WHERE
                                              deleted = 0";
                                        $foreign_values = $this->db->fetch_array($sql);

                                        // Build options
                                        $options = "";
                                        foreach ($foreign_values as $row) {
                                            $options .= "<option value='$row[id]'>$row[name]</option>"; // Build option from row data and add to options string.
                                        }

                                        $newInput = $input -> select($options);

                                    } else {
                                        $newInput = $this -> input($c, $input);
                                    }

                                    echo "<div class='form__group-items-group'>$newInput</div>";
                                }
                            }

                        echo "</div>
                        </div>";

                    }

                    elseif ($col['Key'] === 'MUL') { // Is foreign key.

                        $params = array(''.$conf['table_name'].'');
                        $sql = "SELECT TABLE_NAME,
                                  COLUMN_NAME,
                                  CONSTRAINT_NAME,
                                  REFERENCED_TABLE_NAME,
                                  REFERENCED_COLUMN_NAME
                                  FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                                WHERE TABLE_NAME = ?";
                        $res = $this -> db -> fetch_array($sql, $params);
                        foreach($res as $c) {
                            if ($c['REFERENCED_TABLE_NAME']) {
                                $foreign_table = $c['REFERENCED_TABLE_NAME'];
                                break;
                            }
                        }

                        // Get values from foreign table.
                        if (isset($foreign_table)) {
                            $sql = "SELECT *
                                      FROM " . $foreign_table;
                            $foreign_values = $this->db->fetch_array($sql);

                            // Build select box options from foreign values.
                            $options = "";
                            foreach ($foreign_values as $row) {
                                $options .= "<option value='$row[id]'>$row[name]</option>"; // Insert option.
                            }

                            // Build select box and insert options.
                            echo $input -> select($options);
                        }
                    } else {
                        echo $this -> input($col, $input);
                    }
                }
            }

        }

        $submitChild = array_key_exists('_button_child', $labels)
            ? $labels['_button_child']
            : 'Submit';
        echo "<div class='form__group--right'>
                <button class='button__raised--primary'>$submitChild</button>
            </div>
        </form>";
    }

    /**
     * @param $title
     * @param string $subTitle
     *
     * @return string
     */
    public function title($title, $subTitle = '') {
        $titleString = "<div class='form__group--title'>";
        $titleString .= "<span class='title'>$title</span>";
        if ($subTitle) $titleString .= "<span class='subtitle'>$subTitle</span>";
        $titleString .= "</div>";

        return $titleString;
    }

    /**
     * @param $column
     * @param $config
     *
     * @return string
     */
    public function input($column, $config) {

        $input = new Input();
        $input -> id = $config -> id;
        $input -> name = $config -> name;
        $input -> label = $config -> label;
        $input -> required = $config -> required;
        $input -> contained = $config -> contained;

        if ($column['Comment'] === 'file') { // Comment - file upload.
            return $input -> image();
        }

        elseif (startsWith($column['Type'], 'varchar')) { // Varchar - Regular text field.

            switch($column['Field']) {
                case 'email': $type = 'email'; break;
                case 'password': $type = 'password'; break;
                default: $type = 'text';
            }

            return $input -> field($type);

        }

        elseif ($column['Type'] == 'tinytext') { // Tinytext - small textarea.

            return $input -> textarea(3);

        }

        elseif ($column['Type'] == 'text') { // Text - medium-sized textarea.

            return $input -> textarea(5);

        }

        elseif ($column['Type'] == 'tinyint(1)') { // Tinyint(1) - switch.

            return $input ->switch($column['Default']);

        }

        elseif ($column['Type'] == 'int(11)') { // Int - number field.

            return $input->number();

        }
    }
}

function startsWith($haystack, $needle) {
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function debug ($arr) {
    foreach ($arr as $key) {
        var_dump($key);
        echo "<br><br>";
    }
}

//// Input class
class Input {

    public $id;
    public $name;
    public $label;
    public $required;
    public $value = '';
    public $contained = true;

    /**
     * @param string $type
     *
     * @return string
     */
    public function field($type = 'text') {

        if (!isset($this->name)) {
            $this -> name = $this -> id;
        }

        $field = "
            <label for='$this->id'>$this->label</label>
            <input
                type='$type'
                name='$this->name'
                id='$this->id'
                $this->required
                value='$this->value'
            >
        ";

        if ($this->contained) {
            $field = $this -> contain($field);
        }

        return $field;
    }

    /**
     * @param int $rows
     *
     * @return string
     */
    public function textarea($rows = 1) {

        if (!isset($this->name)) {
            $this -> name = $this -> id;
        }

        $textarea = "
            <label for='$this->id'>$this->label</label>
            <textarea
                name='$this->name'
                id='$this->id'
                rows='$rows'
                $this->required
            >$this->value</textarea>
        ";

        if ($this->contained) {
            $textarea = $this -> contain($textarea);
        }

        return $textarea;
    }

    /**
     * @return string
     */
    public function number() {

        if (!isset($this->name)) {
            $this -> name = $this -> id;
        }

        $number = "
            <label for='$this->id'>$this->label</label>
            <input
                type='number'
                name='$this->name'
                id='$this->id'
                value='$this->value'
                $this->required
            >
        ";

        if ($this->contained) {
            $number = $this -> contain($number);
        }

        return $number;
    }

    /**
     * @param string $options
     * @param bool $hovering
     *
     * @return string
     */
    public function select($options = "", $hovering = true) {

        if (!isset($this->name)) {
            $this -> name = $this -> id;
        }

        $label_class = $hovering
            ? "class='hovering'"
            : "";

        $select = "
            <label for='$this->id' $label_class>$this->label</label>
            <select
                name='$this->name'
                id='$this->id'
                $this->required
            >
                <option value='0'>None</option>
                $options
            </select>
        ";

        if ($this->contained) {
            $select = $this -> contain($select, 'form__group--select');
        }

        return $select;
    }

    /**
     * @param bool $checked
     *
     * @return string
     */
    public function switch($checked = false) {

        if (!isset($this->name)) {
            $this -> name = $this -> id;
        }

        $switch_checked = $checked
            ? 'checked'
            : '';

        $switch = "
            <div class='switch'>
                <label for='$this->id'>$this->label</label>
                <input
                    type='checkbox'
                    name='$this->name'
                    id='$this->id'
                    value='1'
                    $this->required
                    $switch_checked
                >
            </div>
        ";

        if ($this->contained) {
            $switch = $this -> contain($switch, 'form__group--switch');
        }

        return $switch;
    }

    public function image($multiple = true) {

        if (!isset($this->name)) {
            $this->name = $this->id;
        }
        $multiple_attribute = $multiple
            ? 'multiple'
            : '';

        $icon = Icon::build('cloud_upload');
        $image = "
            <div class='file__image'>
                <h4 class='file__title'>$this->label</h4>
                <label for='$this->id'>
                    $icon
                    <span class='file__label'>Choose file(s)</span>
                </label>
                <input
                    type='file'
                    name='$this->name'
                    id='$this->id'
                    $this->required
                    $multiple_attribute
                >
            </div>
        ";

        if ($this->contained) {
            $image = $this -> contain($image, 'form__group--image');
        }

        return $image;
    }

    public function contain($input, $className = 'form__group') {
        $contained = "<div class='$className'>$input</div>";
        return $contained;
    }
}

class Icon {

    public const ICONS = array(
        'arrow_back' => '<i class="material-icons">arrow_back</i>',
        'cloud_upload' => '<i class="material-icons">cloud_upload</i>',
        'menu' => '<i class="material-icons">menu</i>',
    );

    static function build($icon) {
        return self::ICONS[$icon];
    }
}