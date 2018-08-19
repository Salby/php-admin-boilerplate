<?php

require_once('init.php');

$mode = isset($_REQUEST['mode']) && !empty($_REQUEST['mode']) ? $_REQUEST['mode'] : "";

switch (strtoupper($mode)) {

    case 'GET_CITY':
        $crud = new crud('user');
        $city_list = $crud -> get([
            'parameters' => [
                'deleted' => 0
            ],
            'exclude' => [
                'blog',
                'user_session'
            ]
        ]);
        var_dump($city_list);
        break;

}