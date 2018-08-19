<?php

function links($links, $activeName) {
    $items = "";
    $activeName = explode(' ', $activeName)[0];
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

    <nav class="drawer" id="drawer-main">
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
            <li class="drawer__list-title">Product</li>
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
        <ul class="drawer__list">
            <li class="drawer__list-title">Blog</li>
        <?=links([
            [
                'Posts',
                'posts.php',
                'library_books'
            ],
            [
                'Tags',
                'tags.php',
                'label'
            ]
        ], $pageTitle)?>
        </ul>
        <ul class="drawer__list--bottom">
            <?=links([
                [
                    'Log out',
                    'index.php?action=logout',
                    'exit_to_app'
                ]
            ], $pageTitle)?>
        </ul>
    </nav>

    <header class="appbar">
        <button id="drawer-open" data-drawer="drawer-main" class="button__icon--light"><i class="material-icons">menu</i></button>
        <h1><?=$pageTitle?></h1>
    </header>

    <!--<main id="view-container"></main>

    <script src="assets/script.js"></script>
    <script>
        new Views('view-container');
    </script>-->