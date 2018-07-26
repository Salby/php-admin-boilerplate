<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 7/25/18
 * Time: 8:45 PM
 */

require_once('incl/init.php');

if ($auth -> auth_role === 'admin') :

$category = new Category();

$mode = isset($_REQUEST['mode']) && !empty($_REQUEST['mode']) ? $_REQUEST['mode'] : '';

switch (strtoupper($mode)) {

    default:
    case 'LIST':
        $pageTitle = 'Categories';
        require_once('incl/header.php');
        ?>

    <main>
        <div class="card">
            <div class="card__header">
                <ul class="header__row">
                    <li><ul class="header__row-list--left">
                            <li><h1>List</h1></li>
                        </ul></li>
                    <li><ul class="header__row-list--right">
                            <li><a href="categories.php?mode=edit" class="button__icon--primary"><i class="material-icons">add</i></a></li>
                        </ul></li>
                </ul>
                <ul class="header__row--contextual" id="categories-contextual">
                    <li><ul class="header__row-list--left">
                            <li class="contextualAmount"></li>
                        </ul></li>
                    <li><ul class="header__row-list--right">
                            <li><button id="categories-delete" class='button__icon--dark'>
                                    <i class='material-icons'>delete</i>
                                </button></li>
                        </ul></li>
                </ul>
            </div>
            <table class="table" id="categories" data-contextual="categories-contextual">
            </table>
            <div class="table-footer">
                <button class="table-footer__next" id="categories-next"><i class="material-icons">chevron_right</i></button>
                <button class="table-footer__prev" id="categories-prev"><i class="material-icons">chevron_left</i></button>
                <p class="table-footer__status" id="categories-status"></p>
            </div>
        </div>
    </main>

    <script src="assets/script.js"></script>
    <script>
        new Table('categories', {
          source: {
            url: 'categories.php?mode=getlist',
            limit: 10,
            max: <?=$db->get_amount('category')?>
          }
        });
        let deleteDialog = new Dialog({
          title: 'Caution.',
          message: 'Are you sure you want to delete the selected category/categories?',
          actions: {
            confirm: 'TableActions.delete(\'categories.php?mode=delete\', \'categories\'); Dialog.close(deleteDialog.dialog)',
            cancel: 'Dialog.close(deleteDialog.dialog)'
          }
        });
        document.getElementById('categories-delete').addEventListener('click', () => Dialog.open(deleteDialog.dialog));
    </script>

        <?php
        break;


    case 'EDIT':

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $exceptions = array(
            'deleted' => ''
        );
        $labels = array();
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $pageTitle = 'Categories · Edit';
            $form_source = $category -> get_item($_POST['id']);
            $labels['_form_title'] = 'Edit category';
            $exceptions['deleted'] = '<input type="hidden" name="id" value="'.$_POST['id'].'">';
        } else {
            $pageTitle = 'Categories · Create';
            $form_source = array();
            $labels['_form_title'] = 'New category';
        }
        require_once('incl/header.php');
        $form = new form_builder();
        ?>

        <main>
            <div class="card">
                <?php
                $form -> build([
                    'table_name' => 'category',
                    'action' => 'categories.php?mode=save',
                    'method' => 'post',
                    'source' => $form_source
                ], $labels, $exceptions);
                ?>
            </div>
        </main>

        <script src="assets/script.js"></script>
        <script>
            new Form('category');
        </script>

        <?php
        break;


    case 'SAVE':

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $category -> id = isset($_POST['id']) && !empty($_POST['id'])
            ? $_POST['id']
            : 0;
        $category -> name = $_POST['category_name'];
        $category -> image = $_POST['category_image'];

        $category -> save(DOCROOT.'/cms/upload/');
        header('Location: categories.php');

        break;


    case 'DELETE':

        $selected = filter_input(INPUT_POST, 'selected', FILTER_SANITIZE_STRING);
        if (substr_count($selected, ',')) {
            $selected = explode(',', $selected);
        } else {
            $selected = array($selected);
        }
        $category -> delete($selected);

        $str_end = count($selected) > 1
            ? ' categories'
            : ' category';
        echo "Deleted ".count($selected).$str_end;

        break;


    case 'GETLIST':

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $cat_list = $category -> get_list([
            'limit' => $_POST['limit'],
            'offset' => $_POST['offset']
        ]);

        ?>

    <thead>
        <tr>
            <th class="select"><input type="checkbox" name="categories" class="table__checkbox master"></th>
            <th>Name</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($cat_list as $cat) : ?>
        <tr>
            <td class="select"><input type="checkbox" name="categories" value="<?=$cat['id']?>" class="table__checkbox"></td>
            <td><?=$cat['name']?></td>
            <td align="right" class="options">
                <div class="menu--right">
                    <button><i class="material-icons">more_vert</i></button>
                    <ul>
                        <li><form action="categories.php?mode=edit" method="post">
                                <input type="hidden" name="id" value="<?=$cat['id']?>">
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

endif;