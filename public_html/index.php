<?php
require_once('incl/init.php');

$users = $crud -> read([
    'table' => 'product',
]);

var_dump($users);