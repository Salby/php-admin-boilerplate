<?php

class form_builder extends dblyze {

    public $labels;
    public $exceptions;

    protected $db;

    public function __construct() {
        // Call database.
        global $db;
        $this -> db = $db;
        // Call parent constructor.
        parent::__construct();
    }

    public function build($config) {
        $defaults = [
            'labels' => [],
            'exceptions' => []
        ];
        $config = array_merge($defaults, $config);

        $this -> labels = $config['labels'];
        $this -> exceptions = $config['exceptions'];

        $table_info = parent::table_info($config['table']);

        $form = "<form
            class='form__main'
            id='$config[table_name]'
            action='$config[action]'
            method='$config[method]'
            novalidate
            enctype='multipart/form-data'
        >";

        if (!empty($this->labels['__form_title'])) {
            $subtitle = !empty($this->labels['__form_subtitle'])
                ? $this->labels['__form_subtitle']
                : "";
            echo $this -> title($this->labels['__form_title'], $subtitle);
        }

        // Iterate over table info and add inputs.
        foreach ($table_info as $column) {
            // Don't add primary keys.
            if ($column['Key'] !== 'PRI') {

                // Define input id.
                $id = $config['table'].'_'.$column['Field'];
                // Define input label.
                $label = array_key_exists($column['Field'], $this->labels)
                    // Set label.
                    ? $this->labels[$column['Field']]
                    // Use styled column name as label.
                    : ucfirst(str_replace('_', ' ', $column['Field']));
                // Define input required state.
                $required = $column['Null'] === 'NO'
                    // Required.
                    ? "required"
                    // Not required.
                    : "";
                // Define input value.
                $value = array_key_exists($column['Field'], $config['source'])
                    // Set value.
                    ? $config['source'][$column['Field']]
                    // No value.
                    : null;

                // Check if input is in exceptions.
                if (array_key_exists($column['Field'], $this->exceptions)) {
                    if ($this->exceptions[$column['Field']]) {
                        // Set input as  defined in exception.
                        $input = $this->exceptions[$column['Field']];
                        // Insert handled input.
                        echo $this -> handle_exception(
                            $input,
                            $id,
                            $label,
                            $required,
                            $value
                        );
                    }
                } else {
                    // TODO: Handle many-to-many case.
                    // TODO: Handle one-to-many case.
                    // TODO: Handle regular input cases.
                }

            }
        }

        // End form tag.
        $form .= "</form>";

        // Return finished form.
        return $form;
    }

    public function many_to_many($column) {
        $foreign_table = $column['Field'];
        $columns = $column['Columns'];
        $title = ucfirst(str_replace('_', ' ', $foreign_table));

        $class_name = count($columns) === 1
            // One column - inline inputs.
            ? "form__group-items"
            // More columns - inputs grouped in rows.
            : "form__group-items--rows";

        // Add some initial markup.
        $many_to_many = "<div class='$class_name form_items'>
                            <div class='title'>$title</div>
                            <div class='form_items_model'>";

        foreach ($columns as $column) {
            // Don't add primary keys.
            if ($column['Key'] !== 'PRI') {
                // Define input id.
                $id = $foreign_table.'_'.$column['Field'];
                // Define input name.
                $name = $id.'[]';
                // Define input label.
                $label = array_key_exists($column['Field'], $this->labels)
                    // Set label.
                    ? $this->labels[$column['Field']]
                    // Use styled column name as label.
                    : ucfirst(str_replace('_', ' ', $column['Field']));

                // TODO: Handle one-to-many.
                // TODO: Handle regular inputs.
            }
        }

        // Return input(s).
        return $many_to_many;
    }

    public function handle_exception($input, $id, $label, $required, $value) {
        if (strpos($input, '___id___'))
            // Replace id placeholder with actual id.
            $input = str_replace('___id___', $id, $input);
        if (strpos($input, '___label___'))
            // Replace label placeholder with actual label.
            $input = str_replace('___label___', $label, $input);
        if (strpos($input, '___required___'))
            // Replace required placeholder with actual required state.
            $input = str_replace('___required___', $required, $input);
        if (strpos($input, '___value___'))
            // Replace value placeholder with actual value.
            $input = str_replace('___value___', $value, $input);

        // Return finished input.
        return $input;
    }

    public function title($title, $subtitle) {
        $title_string = "< class='form__group--title'>";
        $title_string .= "<span class='title'>$title</span>";
        if ($subtitle) $title_string .= "<span class='subtitle'>$subtitle</span>";
        $title_string .= "</div>";
        return $title_string;
    }

    public function foreign_data($config) {
        $defaults = [
            'order_by' => '',
            'filter_deleted' => true
        ];
        $config = array_merge($defaults, $config);

        // Build SQL query.
        $sql = "SELECT *
                  FROM $config[table]";

        if ($config['filter_deleted'])
            $sql .= " WHERE deleted = 0";
        if (!empty($config['order_by']))
            $sql .= " ORDER BY $config[order_by]";

        // Return foreign data.
        return $this -> db -> fetch_array($sql);
    }

}