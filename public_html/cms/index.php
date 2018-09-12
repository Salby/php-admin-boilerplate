<?php

require_once('incl/init.php');
$pageTitle = 'Home';
require_once('incl/header.php');

$home_data = [
    'users' => [
        'name' => 'Users',
        'data' => $db -> get_amount('user'),
        'link' => 'users.php'
    ],
    'products' => [
        'name' => 'Products',
        'data' => $db -> get_amount('user'),
        'link' => 'products.php'
    ],
    'posts' => [
        'name' => 'Blog posts',
        'data' => $db -> get_amount('blog'),
        'link' => 'posts.php'
    ]
];

echo "<main class='grid__home'>";

foreach ($home_data as $card) {
    echo "<div class='card'>
        <h5>$card[name]</h5>
        <p class='data'>$card[data]</p>
        <div class='actions'>
            <a href='$card[link]' class='button__icon--accent'><i class='material-icons'>arrow_forward</i></a>
        </div>
    </div>";
}

echo "</main>";

?>

<script src="assets/script.js"></script>
