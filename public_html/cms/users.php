<?php

require_once('incl/init.php');

if ($auth -> auth_role === 'admin') :

$user = new User();

$mode = isset($_REQUEST['mode']) && !empty($_REQUEST['mode']) ? $_REQUEST['mode'] : '';

switch (strtoupper($mode)) {
    default:
    case 'LIST':
        $pageTitle = "Users";
        require_once('incl/header.php');
        echo html_tool::table_frame([
            'table' => 'user',
            'element_id' => 'users',
            'max' => $db->get_amount('user')
        ]);
        break;

    case 'EDIT':

        $exceptions = array(
            'deleted' => '',
            'salt' => '',
        );
        $labels = array();
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $pageTitle = "Users · Edit";
            $form_source = $user -> get_item($_POST['id']);
            $exceptions['password'] = '<input type="hidden" name="id" value="'.$_POST['id'].'">';
            $labels['__form_title'] = 'Edit user';
        } else {
            $pageTitle = "Users · Create";
            $form_source = array();
            $exceptions['suspended'] = '';
            $labels['__form_title'] = 'New user';
        }
        require_once('incl/header.php');
        $form = new form_builder();
        ?>

    <main>
        <div class="card">
            <?php
            echo $form -> build([
                'table' => 'user',
                'action' => 'users.php?mode=save',
                'method' => 'post',
                'source' => $form_source,
                'labels' => $labels,
                'exceptions' => $exceptions,
                'exclude' => [
                    'blog'
                ]
            ]);
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
        $user -> id = isset($_POST['user_id']) && !empty($_POST['user_id'])
            ? $_POST['user_id']
            : 0;
        $user -> name = $_POST['user_name'];
        $user -> password = isset($_POST['user_password']) && !empty($_POST['user_password'])
            ? $_POST['user_password']
            : '';
        $user -> email = $_POST['user_email'];
        $user -> address = $_POST['user_address'];
        $user -> city = $_POST['user_city'];
        $user -> avatar = $_FILES['user_avatar'];
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

        $user_list = $user -> get_list([
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
                <?php foreach ($user_list as $user) : ?>
                    <tr>
                        <td class="select"><input type="checkbox" name="users" class="table__checkbox" value="<?=$user['id']?>"></td>
                        <td class="type--align-right"><?=$user['id']?></td>
                        <td><?=$user['name']?></td>
                        <td><?=$user['email']?></td>
                        <td><?=$user['role']?></td>
                        <td class="type--align-right"><?=$user['suspended']?></td>
                        <td align="right" class="options">
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
