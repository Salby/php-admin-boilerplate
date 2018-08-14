<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 7/20/18
 * Time: 2:39 PM
 */
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login // CMS</title>
        <link rel="stylesheet" href="assets/master.css">
    </head>
    <body class="center">

        <form id="login" class="form__main" action="index.php" method="post" novalidate>
            <div class="form__group--title">
                <span class="title">Login</span>
            </div>
            <div class="form__group">
                <input type="text" name="login_username" id="login_username" required>
                <label for="login_username">Username</label>
            </div>
            <div class="form__group">
                <input type="password" name="login_password" id="login_password" required>
                <label for="login_password">Password</label>
            </div>
            <div class="form__group--right">
                <button class="button__raised--primary">Login</button>
            </div>
        </form>


        <script src="assets/script.js"></script>
        <script>
            new Form('login');
        </script>
    </body>
</html>