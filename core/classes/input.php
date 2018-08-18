<?php


class Input {

    public $id;
    public $name;
    public $label;
    public $required;
    public $value;
    public $contained = true;

    public $config = array();

    /**
     * # Config
     *
     * __String__ --_id_
     *
     * __String__ _name_
     *
     * __String__ --_label_
     *
     * __String__ --_required_
     *
     * __String__ _value_
     *
     * __Bool__ _contained_
     *
     * # Example
     *
     * ```
     * $input = new Input([
     *      'id' => 'input_id',
     *      'name' => 'input_name',
     *      'label' => 'label',
     *      'required' => 'required',
     *      'value' => 'abcdefg',
     *      'contained' => true
     * ]);
     * ```
     *
     * @param array $config
     */
    public function __construct($config) {
        $defaults = [
            'contained' => true
        ];
        $config = array_merge($defaults, $config);

        $this -> id = $config['id'];
        $this -> name = isset($config['name'])
            ? $config['name']
            : $config['id'];
        $this -> label = $config['label'];
        $this -> required = $config['required'];
        $this -> value = isset($config['value'])
            ? $config['value']
            : "";
        $this -> contained = isset($config['contained'])
            ? $config['contained']
            : true;
    }

    /**
     * @param string input - String to contain.
     * @param string class_name - Class name given to surrounding DIV.
     *
     * @return string
     */
    public function contain($config) {
        $defaults = array(
            'class_name' => 'form__group'
        );
        $config = array_merge($defaults, $config);

        return "<div class='$config[class_name]'>$config[input]</div>";
    }

    /**
     * # Regular input field.
     *
     * ## Example
     *
     * ```
     * $input = new Input([...]);
     * echo $input -> field('email')
     * ```
     *
     * @param string $type - Defines input type. Could be email, password, etc.
     *
     * @return string $input
     */
    public function field($type = 'text') {
        $input = "
            <input
                type='$type'
                name='$this->name'
                id='$this->id'
                $this->required
                value='$this->value'
            >    
            <label for='$this->id'>$this->label</label>
        ";

        if ($this->contained)
            $input = $this -> contain([ 'input' => $input ]);

        return $input;
    }

    /**
     * # Textarea
     *
     * A textarea with defined rows
     *
     * ## Example
     *
     * ```
     * $input = new Input([...]);
     * echo $input -> textarea(5);
     * ```
     *
     * @param int $rows - Number of initial rows.
     *
     * @return string $input
     */
    public function textarea($rows = 1) {
        $input = "
            <textarea
                name='$this->name'
                id='$this->id'
                rows='$rows'
                $this->required
            >$this->value</textarea>
            <label for='$this->id'>$this->label</label>
        ";

        if ($this->contained)
            $input = $this -> contain([ 'input' => $input ]);

        return $input;
    }

    /**
     * # Number
     *
     * A regular number field.
     *
     * ## Example
     *
     * ```
     * $input = new Input([...]);
     * echo $input -> number();
     * ```
     *
     * @return string $input
     */
    public function number() {
        $input = "
            <input
                type='number'
                name='$this->name'
                id='$this->id'
                value='$this->value'
                $this->required
            >
            <label for='$this->id'>$this->label</label>
        ";

        if ($this->contained)
            $input = $this -> contain([ 'input' => $input ]);

        return $input;
    }

    /**
     * # Select box
     *
     * ## Config
     * __String__ --options - Select box options.
     *
     * __Bool__ hovering - Toggles floating label.
     *
     * ## Examples
     * ```
     * $input = new Input([...]);
     * echo $input -> select([
     *      'options' => '<option value="1">Foo</option>'
     * ]);
     * ```
     *
     * @param array $config
     *
     * @return string $input.
     */
    public function select($config) {
        $defaults = [
            'options' => '',
            'hovering' => true
        ];
        $config = array_merge($defaults, $config);

        $label_class = $config['hovering']
            ? "class='hovering'"
            : "";

        $input = "
            <select
                name='$this->name'
                id='$this->id'
                $this->required
            >
                <option value='0'>None</option>
                $config[options]
            </select>
            <label for='$this->id' $label_class>$this->label</label>
        ";

        if ($this->contained)
            $input = $this -> contain([
                'input' => $input,
                'class_name' => 'form__group--select'
            ]);

        return $input;
    }

    public function search_box($json, $user_add) {
        $input = "
            <button type='button' class='input'>".ucfirst(str_replace('_', ' ', $this->value))."</button>
            <label for='$this->id' class='label'>$this->label</label>
            <div class='search-box' data-list='$json' data-user-add='$user_add'>
                <input class='search-box__input' type='text'>
                <div class='search-box__container'></div>
            </div>
            <input type='hidden' name='$this->name' value='$this->value'>
        ";

        if ($this->contained)
            $input = $this -> contain([
                'input' => $input,
                'class_name' => 'form__group--searchbox'
            ]);
        else
            $input = $this -> contain([
                'input' => $input,
                'class_name' => 'searchbox'
            ]);

        return $input;
    }

    /**
     * @param bool $checked - Toggles switch state.
     *
     * @return string $input
     */
    public function toggle($checked = false) {
        $toggle_checked = $checked
            ? "checked"
            : "";

        $input = "
            <div class='switch'>
                <label for='$this->id'>$this->label</label>
                <input
                    type='checkbox'
                    name='$this->name'
                    id='$this->id'
                    value='1'
                    $this->required
                    $toggle_checked
                >
            </div>
        ";

        if ($this->contained)
            $input = $this -> contain([
                'input' => $input,
                'class_name' => 'form__group--switch'
            ]);

        return $input;
    }

    /**
     * @param bool $multiple - Allows for input to accept multiple files.
     *
     * @return string $input
     */
    public function image($multiple = true) {
        $multiple_attr = $multiple
            ? "multiple"
            : "";

        $input = "
            <div class='file__image'>
                <h4 class='file__title'>$this->label</h4>
                <label for='$this->id'>
                    <i class='material-icons'>cloud_upload</i>
                    <span class='file__label'>Choose file(s)</span>
                </label>
                <input
                    type='file'
                    accept='image/*'
                    name='$this->name'
                    id='$this->id'
                    $this->required
                    $multiple_attr
                >
            </div>
        ";

        if ($this->contained)
            $input = $this -> contain([
                'input' => $input,
                'class_name' => 'form__group--file'
            ]);

        return $input;
    }

}