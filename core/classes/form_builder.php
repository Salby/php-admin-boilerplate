<?php

class form_builder extends dblyze {

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

        $labels = $config['labels'];
        $exceptions = $config['exceptions'];

        $table_info = parent::table_info($config['table']);

        $form = "<form
            class='form__main'
            id='$config[table_name]'
            action='$config[action]'
            method='$config[method]'
            novalidate
            enctype='multipart/form-data'
        >";
    }

}