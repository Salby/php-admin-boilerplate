<?php

require_once('init.php');

$mode = isset($_REQUEST['mode']) && !empty($_REQUEST['mode']) ? $_REQUEST['mode'] : "";

switch (strtoupper($mode)) {

    case 'GET_CITY':
        $params = [
            $_POST['zipcode']
        ];
        $sql = "SELECT *
                  FROM city
                WHERE
                  zipcode = ?";
        $validate = $db -> fetch_array($sql, $params);
        $response = !empty($validate)
            ? $validate['name']
            : "false";
        break;

}