<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 7/30/18
 * Time: 1:15 PM
 */

require_once('incl/init.php');

$product = new Product();

$mode = isset($_REQUEST['mode']) && !empty($_REQUEST['mode']) ? $_REQUEST['mode'] : "";

switch (strtoupper($mode)) {


    default:
    case 'LIST':

        $pageTitle = "Products";
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
                            <li><a href="products.php?mode=edit" class="button__icon--primary"><i class="material-icons">add</i></a></li>
                        </ul></li>
                </ul>
                <ul class="header__row--contextual" id="products-contextual">
                    <li><ul class="header__row-list--left">
                            <li class="contextualAmount"></li>
                        </ul></li>
                    <li><ul class="header__row-list--right">
                            <li><button id="products-delete" class='button__icon--dark'>
                                    <i class='material-icons'>delete</i>
                                </button></li>
                        </ul></li>
                </ul>
            </div>
            <table class="table" id="products" data-contextual="products-contextual">
            </table>
            <div class="table-footer">
                <button class="table-footer__next" id="products-next"><i class="material-icons">chevron_right</i></button>
                <button class="table-footer__prev" id="products-prev"><i class="material-icons">chevron_left</i></button>
                <p class="table-footer__status" id="products-status"></p>
            </div>
        </div>
    </main>

    <script src="assets/script.js"></script>
    <script>
        new Table('products', {
          source: {
            url: 'products.php?mode=getlist',
            limit: 10,
            max: <?=$db->get_amount('product')?>
          }
        });
        let deleteDialog = new Dialog({
          title: 'Caution.',
          message: 'Are you sure you want to delete the selected product(s)?',
          actions: {
            confirm: 'TableActions.delete(\'products.php?mode=delete\', \'products\'); Dialog.close(deleteDialog.dialog)',
            cancel: 'Dialog.close(deleteDialog.dialog)'
          }
        });
        document.getElementById('products-delete').addEventListener('click', () => Dialog.open(deleteDialog.dialog));
    </script>

        <?php
        break;


    case 'EDIT':

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $labels = array(
            'is_private' => 'Private',
            'category_category_id' => 'Category'
        );
        $exceptions = array(
            'deleted' => ''
        );
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $pageTitle = "Products · Edit";
            $form_source = $product -> get_item($_POST['id']);
            $labels['_form_title'] = "Edit product";
        } else {
            $pageTitle = "Products · Create";
            $form_source = array();
            $labels['_form_title'] = "New product";
        }
        require_once('incl/header.php');
        $form = new form_builder();
        ?>
    <main><div class="card">
            <?php
            $form -> build([
                'table_name' => 'product',
                'action' => 'products.php?mode=save',
                'method' => 'post',
                'source' => $form_source
            ], $labels, $exceptions);
            ?>
        </div></main>
    <script src="assets/script.js"></script>
    <script>new Form('product')</script>
        <?php
        break;


    case 'SAVE':

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $product -> id = isset($_POST['id']) && !empty($_POST['id'])
            ? $_POST['id']
            : 0;
        $product -> name = $_POST['product_name'];
        $product -> description = $_POST['product_description'];
        $product -> summary = $_POST['product_summary'];
        $product -> image = $_FILES['product_image']['tmp_name'];
        $product -> price = $_POST['product_price'];
        $product -> is_private = $_POST['product_is_private'];

        $product -> category = $_POST['product_category_id'];

        $product -> save(DOCROOT.'/cms/upload/');
        header('Location: products.php');

        break;


    case 'DELETE':

        $rows = filter_input(INPUT_POST, 'selected', FILTER_SANITIZE_STRING);
        if (substr_count(',', $rows)) {
            $rows = explode(',', $rows);
        } else {
            $rows = array($rows);
        }
        $product -> delete($rows);

        $str_end = count($rows) > 1
            ? " products"
            : " product";
        echo "Deleted ".count($rows).$str_end;

        break;


    case 'GETLIST':

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $product_list = $product -> get_list([
            'limit' => $_POST['limit'],
            'offset' => $_POST['offset']
        ]);
        ?>
    <thead>
        <tr>
            <th class="select"><input type="checkbox" name="products" class="table__checkbox master"></th>
            <th>Name</th>
            <th class="type--align-right">Price</th>
            <th>Private</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($product_list as $product) :
        $private = $product['is_private']
            ? "True"
            : "False";
    ?>
        <tr>
            <td class="select"><input type="checkbox" name="products" value="<?=$product['id']?>" class="table__checkbox"></td>
            <td><?=$product['name']?></td>
            <td class="type--align-right"><?=number_format($product['price'], 2, ',', '.')?></td>
            <td><?=$private?></td>
            <td align="right" class="options"><div class="menu--right">
                    <button><i class="material-icons">more_vert</i></button>
                    <ul>
                        <li><form action="products.php?mode=edit" method="post">
                                <input type="hidden" name="id" value="<?=$product['id']?>">
                                <input type="submit" value="Edit">
                            </form></li>
                    </ul>
                </div></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
        <?php
        break;


}