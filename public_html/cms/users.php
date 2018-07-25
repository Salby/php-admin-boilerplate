<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 7/21/18
 * Time: 1:49 PM
 */

require_once('incl/init.php');

if ($auth -> auth_role === 'admin') :

$user = new User();

$mode = isset($_REQUEST['mode']) && !empty($_REQUEST['mode']) ? $_REQUEST['mode'] : '';

switch (strtoupper($mode)) {
    default:
    case 'LIST':
        $pageTitle = "Users";
        require_once('incl/header.php');
        $userList = $user -> get_list();
        ?>

    <main>
        <div class="card">
            <div class="card__header">
                <ul class="header__row">
                    <li><ul class="header__row-list--left">
                            <li><h1>List</h1></li>
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
            </table>
            <div class="table-footer">
                <button class="table-footer__next" id="users-next"><i class="material-icons">chevron_right</i></button>
                <button class="table-footer__prev" id="users-prev"><i class="material-icons">chevron_left</i></button>
                <p class="table-footer__status" id="users-status"></p>
            </div>
        </div>
    </main>

    <script src="assets/script.js"></script>
    <script>
        new Table('users', {
          source: {
            url: 'users.php?mode=getlist',
            limit: 10,
            max: <?=$db -> get_amount('user')?>
          }
        });

        let deleteDialog = new Dialog({
          title: 'Careful.',
          body: 'Are you sure you want to delete the selected user(s)?',
          actions: {
            confirm: 'TableActions.delete(\'users.php?mode=delete\', \'users\'); Dialog.close(deleteDialog.dialog)',
            cancel: 'Dialog.close(deleteDialog.dialog)'
          }
        });
        let deleteButton = document.getElementById('users-delete');
        deleteButton.addEventListener('click', () => {
            Dialog.open(deleteDialog.dialog);
        });
    </script>

        <?php
        break;


    case 'EDIT':

        $exceptions = array(
            'deleted' => '',
            'salt' => ''
        );
        $labels = array();
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $pageTitle = "Users · Edit";
            $form_source = $user -> get_item($_POST['id'], true);
            $exceptions['password'] = '<input type="hidden" name="id" value="'.$_POST['id'].'">';
            $labels['_form_title'] = 'Edit user';
        } else {
            $pageTitle = "Users · Create";
            $form_source = array();
            $exceptions['suspended'] = '';
            $labels['_form_title'] = 'New user';
        }
        require_once('incl/header.php');
        $form = new form_builder();
        ?>

    <main>
        <div class="card">
            <?php
            $form -> build([
                'table_name' => 'user',
                'action' => 'users.php?mode=save',
                'method' => 'post',
                'source' => $form_source
            ], $labels, $exceptions);
            ?>
        </div>
    </main>
    <script src="assets/script.js"></script>
    <script>
        new Form('user');
    </script>


        <?php
        break;

    case 'SAVE':

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $user -> id = isset($_POST['id']) && !empty($_POST['id'])
            ? $_POST['id']
            : 0;
        $user -> username = $_POST['user_username'];
        $user -> password = isset($_POST['user_password']) && !empty($_POST['user_password'])
            ? $_POST['user_password']
            : '';
        $user -> email = $_POST['user_email'];
        $user -> address = $_POST['user_address'];
        $user -> role = $_POST['user_role'];
        $user -> suspended = isset($_POST['user_suspended']) && !empty($_POST['user_suspended'])
            ? $_POST['user_suspended']
            : 0;

        $user -> save(DOCROOT.'/cms/upload/');
        header('Location: users.php');

        break;

    case 'DELETE':

        $selected = filter_input(INPUT_POST, 'selected', FILTER_SANITIZE_STRING);
        if (substr_count($selected, ',')) {
            $selected = explode(',', $selected);
        } else {
            $selected = array($selected);
        }
        $user -> delete($selected);

        $str_end = count($selected) > 1
            ? ' users'
            : ' user';
        echo "Deleted ".count($selected).$str_end;

        break;


    case 'GETLIST':

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_NUMBER_INT);

        $userList = $user -> get_list([
            'limit' => $_POST['limit'],
            'offset' => $_POST['offset']
        ]);
        ?>
                <thead>
                    <tr>
                        <th class='select'><input type='checkbox' name='users' class='table__checkbox master'></th>
                        <th class="type--align-right">Id</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th class="type--align-right">Suspended</th>
                        <th></th>
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
                        <td class="options">
                            <div class="menu--right">
                                <button><i class="material-icons">more_vert</i></button>
                                <ul>
                                    <li><form action="users.php?mode=edit" method="post">
                                            <input type="hidden" name="id" value="<?=$user['id']?>">
                                            <input type="submit" value="Edit">
                                        </form></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
        <?php
        break;
}
?>

<?php
endif;
