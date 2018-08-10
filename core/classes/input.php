<?php

/**
 * # Configuring Input class:
 *
 * - __string__ id
 * - __string__ name
 * - __string__ label
 * - __bool__ required
 * - __string__ value
 * - __bool__ contained
 */
class Input {

    public $config = array();

    public function __construct($config) {
        $defaults = [
            'contained' => true
        ];
        $config = array_merge($defaults, $config);

        $this -> config = $config;

        $this -> config['required'] = $config['required']
            ? "required"
            : "";
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
     * @param string $type - Defines input type. Could be email, password, etc.
     *
     * @return string $input
     */
    public function field($type = 'text') {
        if (!isset($this->config['name']))
            $this -> config['name'] = $this -> config['id'];

        $input = "
            <input
                type='$type'
                name='$this->config[name]'
                id='$this->config[id]'
                $this->config[required]
                value='$this->config[value]'
            >    
            <label for='$this->config[id]'>$this->config[label]</label>
        ";

        if ($this->config['contained'])
            $input = $this -> contain([ 'input' => $input ]);

        return $input;
    }

    /**
     * @param int $rows - Number of initial rows.
     *
     * @return string $input
     */
    public function textarea($rows = 1) {
        if (!isset($this->config['name']))
            $this -> config['name'] = $this -> config['id'];

        $input = "
            <textarea
                name='$this->config[name]'
                id='$this->config[id]'
                rows='$rows'
                $this->config[required]
            >$this->config[value]</textarea>
            <label for='$this->config[id]'>$this->config[label]</label>
        ";

        if ($this->config['contained'])
            $input = $this -> contain([ 'input' => $input ]);

        return $input;
    }

    /**
     * @return string $input
     */
    public function number() {
        if (!isset($this->config['name']))
            $this -> config['name'] = $this -> config['id'];

        $input = "
            <input
                type='number'
                name='$this->config[name]'
                id='$this->config[id]'
                value='$this->config[value]'
                $this->config[required]
            >
            <label for='$this->config[id]'>$this->config[label]</label>
        ";

        if ($this->config['contained'])
            $input = $this -> contain([ 'input' => $input ]);

        return $input;
    }

    /**
     * @param string options - Options to select from.
     * @param bool hovering - Toggles floating label.
     *
     * @return string $input.
     */
    public function select($config) {
        $defaults = [
            'options' => '',
            'hovering' => true
        ];
        $config = array_merge($defaults, $config);

        if (!isset($this->config['name']))
            $this -> config['name'] = $this -> config['id'];

        $label_class = $config['hovering']
            ? "class='hovering'"
            : "";

        $input = "
            <select
                name='$this->config[name]'
                id='$this->config[id]'
                $this->config[required]
            >
                <option value='0'>None</option>
                $config[options]
            </select>
            <label for='$this->config[id]' $label_class>$this->config[label]</label>
        ";

        if ($this->config['contained'])
            $input = $this -> contain([
                'input' => $input,
                'class_name' => 'form__group--select'
            ]);

        return $input;
    }

    /**
     * @param bool $checked - Toggles switch state.
     *
     * @return string $input
     */
    public function toggle($checked = false) {
        if (!isset($this->config['name']))
            $this -> config['name'] = $this -> config['id'];

        $toggle_checked = $checked
            ? "checked"
            : "";

        $input = "
            <div class='switch'>
                <label for='$this->config[id]'>$this->config[label]</label>
                <input
                    type='checkbox'
                    name='$this->config[name]'
                    id='$this->config[id]'
                    value='1'
                    $this->config[required]
                    $toggle_checked
                >
            </div>
        ";

        if ($this->config['contained'])
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
        if (!isset($this->config['name']))
            $this -> config['name'] = $this -> config['id'];

        $multiple_attr = $multiple
            ? "multiple"
            : "";

        $input = "
            <div class='file__image'>
                <h4 class='file__title'>$this->config[label]</h4>
                <label for='$this->config[id]'>
                    <i class='material-icons'>cloud_upload</i>
                    <span class='file__label'>Choose file(s)</span>
                </label>
                <input
                    type='file'
                    accept='image/*'
                    name='$this->config[name]'
                    id='$this->config[id]'
                    $this->config[required]
                    $multiple_attr
                >
            </div>
        ";

        if ($this->config['contained'])
            $input = $this -> contain([
                'input' => $input,
                'class_name' => 'form__group--file'
            ]);

        return $input;
    }

}