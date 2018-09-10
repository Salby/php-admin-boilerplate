<?php

require_once('incl/init.php');

$product = new Product();

$mode = isset($_REQUEST['mode']) && !empty($_REQUEST['mode']) ? $_REQUEST['mode'] : "";

switch (strtoupper($mode)) {


    default:
    case 'LIST':

        $pageTitle = "Products";
        require_once('incl/header.php');
        echo html_tool::table_frame([
            'table' => 'product',
            'element_id' => 'products',
            'max' => $db->get_amount('product')
        ]);
        break;


    case 'EDIT':

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $labels = array(
            'is_private' => 'Private',
            'category_id' => 'Category'
        );
        $exceptions = array(
            'deleted' => ''
        );
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $pageTitle = "Products · Edit";
            $form_source = $product -> get_item($_POST['id']);
            $labels['__form_title'] = "Edit product";
            $labels['__submit_child'] = "Save changes";
        } else {
            $pageTitle = "Products · Create";
            $form_source = array();
            $labels['__form_title'] = "New product";
            $labels['__submit_child'] = "Create";
        }
        require_once('incl/header.php');
        $form = new form_builder();
        ?>
    <main><div class="card">
            <?=
            $form -> build([
                'table' => 'product',
                'action' => 'products.php?mode=save',
                'method' => 'post',
                'source' => $form_source,
                'labels' => $labels,
                'exceptions' => $exceptions
            ]);
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