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

        if (!empty($labels['__form_title'])) {
            $subtitle = !empty($labels['__form_subtitle'])
                ? $labels['__form_subtitle']
                : "";
            echo $this -> title($labels['__form_title'], $subtitle);
        }

        // End form tag.
        $form .= "</form>";

        // Return finished form.
        return $form;
    }

    public function title($title, $subtitle) {
        $title_string = "< class='form__group--title'>";
        $title_string .= "<span class='title'>$title</span>";
        if ($subtitle) $title_string .= "<span class='subtitle'>$subtitle</span>";
        $title_string .= "</div>";
        return $title_string;
    }

}