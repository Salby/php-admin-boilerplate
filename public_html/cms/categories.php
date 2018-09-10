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
        echo html_tool::table_frame([
            'table' => 'category',
            'element_id' => 'categories',
            'max' => $db->get_amount('category')
        ]);
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
            $labels['__form_title'] = 'Edit category';
        } else {
            $pageTitle = 'Categories · Create';
            $form_source = array();
            $labels['__form_title'] = 'New category';
        }
        require_once('incl/header.php');
        $form = new form_builder();
        ?>

        <main>
            <div class="card">
                <?=
                $form -> build([
                    'table' => 'category',
                    'action' => 'categories.php?mode=save',
                    'method' => 'post',
                    'source' => $form_source,
                    'labels' => $labels,
                    'exceptions' => $exceptions,
                    'exclude' => [
                        'product_category'
                    ]
                ]);
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
        $category -> id = isset($_POST['category_id']) && !empty($_POST['category_id'])
            ? $_POST['category_id']
            : 0;
        $category -> name = $_POST['category_name'];
        $category -> image = $_FILES['category_image'];

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