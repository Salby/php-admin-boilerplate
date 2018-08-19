<?php

require_once('init.php');

$mode = isset($_REQUEST['mode']) && !empty($_REQUEST['mode']) ? $_REQUEST['mode'] : "";

switch (strtoupper($mode)) {

    case 'GET_CITY':
        $crud = new crud('user');
        $city_list = $crud -> get([
            'column_params' => [
                'deleted' => 0
            ],
            'query_params' => [
                'LIMIT' => 1,
                'OFFSET' => 1
            ],
            'exclude' => [
                'blog',
                'user_session'
            ]
        ]);
        var_dump($city_list);
        break;

}