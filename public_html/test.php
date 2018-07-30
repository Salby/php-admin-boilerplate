<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 7/28/18
 * Time: 3:56 PM
 */
require_once('incl/init.php');

$user = new Tag();

$user_list = $user -> get_list([
    'query' => $_GET['q']
]);

foreach ($user_list as $user) {
    var_dump($user);
    echo "<br><br>";
}