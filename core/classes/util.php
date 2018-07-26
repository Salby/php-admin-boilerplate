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

        for ($i = 0; $i < count($config['target']); $i++) {
            $concat = $config['target'][$i][$config['source']];
            $arr = explode($config['separator'], $concat);
            $config['target'][$i][$config['source']] = $arr;
        }
    }

}