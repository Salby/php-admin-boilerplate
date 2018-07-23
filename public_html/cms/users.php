<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 7/21/18
 * Time: 1:49 PM
 */

require_once('incl/init.php');

if ($auth -> auth_role === 'admin') :

$pageTitle = 'Users';

$user = new User();

$mode = isset($_REQUEST['mode']) && !empty($_REQUEST['mode']) ? $_REQUEST['mode'] : '';

switch (strtoupper($mode)) {
    default:
    case 'LIST':
        require_once('incl/header.php');
        $userList = $user -> getList();
        ?>

    <main>
        <div class="card">
            <div class="card__header">
                <ul class="header__row">
                    <li><ul class="header__row-list--left">
                            <li><h1>Users</h1></li>
                        </ul></li>
                    <li><ul class="header__row-list--right">
                            <li><a href="users.php?mode=edit" class="button__icon--primary"><i class="material-icons">add</i></a></li>
                        </ul></li>
                </ul>
                <ul class="header__row--contextual" id="users-contextual">
                    <li><ul class="header__row-list--left">
                            <li class="contextualAmount"></li>
                        </ul></li>
                    <li><ul class="header__row-list--right">
                            <li><button id="users-delete" class='button__icon--dark'>
                                    <i class='material-icons'>delete</i>
                                </button></li>
                        </ul></li>
                </ul>
            </div>
            <table class="table" id="users" data-contextual="users-contextual">
                <thead>
                    <tr>
                        <th class='select'><input type='checkbox' name='users' class='table__checkbox master'></th>
                        <th class="type--align-right">Id</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th class="type--align-right">Suspended</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($userList as $user) : ?>
                    <tr>
                        <td class="select"><input type="checkbox" name="users" class="table__checkbox" value="<?=$user['id']?>"></td>
                        <td class="type--align-right"><?=$user['id']?></td>
                        <td><?=$user['username']?></td>
                        <td><?=$user['email']?></td>
                        <td><?=$user['role']?></td>
                        <td class="type--align-right"><?=$user['suspended']?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="assets/script.js"></script>
    <script>
        new Table('users');

        let deleteButton = document.getElementById('users-delete');
        deleteButton.addEventListener('click', () => {
          let deleteDialog = new Dialog({})
        })
    </script>

        <?php
        break;
}
?>

<?php
endif;
