<?php

function links($links, $activeName) {
    $items = "";
    foreach ($links as $li) {

        $active = strtolower($activeName) === strtolower($li[0])
            ? 'active'
            : '';

        $items .= "<li>
            <a href='$li[1]' class='drawer__link views $active'>
                <i class='material-icons'>$li[2]</i>
                $li[0]
            </a>
        </li>";
    }
    return $items;
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?=$pageTitle?> // CMS</title>
        <link rel="stylesheet" href="assets/master.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    </head>
    <body class="admin">

    <nav class="drawer">
        <div class="drawer__header">
            <h1 class="drawer__title">CMS 2.0</h1>
        </div>
        <ul class="drawer__list">
        <?=links([
            [
                'Home',
                'index.php',
                'home'
            ],
            [
                'Users',
                'users.php',
                'people'
            ]
        ], $pageTitle)?>
        </ul>
        <ul class="drawer__list">
        <?=links([
            [
                'Products',
                'products.php',
                'shopping_cart'
            ],
            [
                'Categories',
                'categories.php',
                'bookmark'
            ]
        ], $pageTitle)?>
        </ul>
    </nav>

    <header class="appbar">
        <h1><?=$pageTitle?></h1>
    </header>

    <!--<main id="view-container"></main>

    <script src="assets/script.js"></script>
    <script>
        new Views('view-container');
    </script>-->