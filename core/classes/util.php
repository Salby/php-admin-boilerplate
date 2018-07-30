<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 7/26/18
 * Time: 12:11 PM
 */

class util {

    static function merge_concat($config) {
        $defaults = array(
            'separator' => '-----'
        );
        $config = array_merge($defaults, $config);

        $source = $config['target'][$config['source']];
        unset($config['target'][$config['source']]);
        $exploded = explode($config['separator'], $source);
        foreach ($exploded as $val) {
            $config['target'][$config['source']] = $val;
        }

    }

    static function search($config) {
        $defaults = array(
            'target' => 'name'
        );
        $config = array_merge($defaults, $config);

        return " AND $config[target] LIKE '%$config[query]%'";
    }

}