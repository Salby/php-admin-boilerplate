<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 7/27/18
 * Time: 5:57 PM
 */

require_once('incl/init.php');

if ($auth -> auth_role === 'admin' || $auth -> auth_role === 'editor') :


$tag = new Tag();

$mode = isset($_REQUEST['mode']) && !empty($_REQUEST['mode']) ? $_REQUEST['mode'] : '';

switch (strtoupper($mode)) {

    default:
    case 'LIST':

        $pageTitle = "Tags";
        require_once('incl/header.php');
        echo html_tool::table_frame([
            'table' => 'tag',
            'element_id' => 'tags',
            'max' => $db->get_amount('tag')
        ]);
        break;


    case 'EDIT':

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $labels = array();
        $exceptions = array(
            'deleted' => ''
        );
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $pageTitle = "Tags · Edit";
            $form_source = $tag -> get_item($_POST['id']);
            $labels['_form_title'] = "Edit tag";
        } else {
            $pageTitle = "Tags · Create";
            $form_source = array();
            $labels['_form_title'] = "New tag";
        }
        require_once('incl/header.php');
        $form = new form_builder();
        ?>
    <main>
        <div class="card">
            <?php
            $form -> build([
                'table_name' => 'tag',
                'action' => 'tags.php?mode=save',
                'method' => 'post',
                'source' => $form_source,
                'standalone' => true
            ], $labels, $exceptions);
            ?>
        </div>
    </main>
    <script src="assets/script.js"></script>
    <script>
        new Form('tag');
    </script>
        <?php
        break;


    case 'SAVE':

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $tag -> id = isset($_POST['id']) && !empty($_POST['id'])
            ? $_POST['id']
            : 0;
        $tag -> name = $_POST['tag_name'];

        $tag -> save();
        header('Location: tags.php');

        break;


    case 'DELETE':

        $rows = filter_input(INPUT_POST, 'selected', FILTER_SANITIZE_STRING);
        if (substr_count($rows, ',')) {
            $rows = explode(',', $rows);
        } else {
            $rows = array($rows);
        }
        $tag -> delete($rows);

        $str_end = count($rows) > 1
            ? ' tags'
            : ' tag';
        echo "Deleted ".count($rows).$str_end;

        break;


    case 'GETLIST':

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $tag_list = $tag -> get_list([
            'limit' => $_POST['limit'],
            'offset' => $_POST['offset']
        ]);
        ?>

    <thead>
        <tr>
            <th class="select"><input type="checkbox" name="tags" class="table__checkbox master"></th>
            <th>Name</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($tag_list as $tag) : ?>
        <tr>
            <td class="select"><input type="checkbox" name="tags" value="<?=$tag['id']?>" class="table__checkbox"></td>
            <td><?=$tag['name']?></td>
            <td align="right" class="options"><div class="menu--right">
                    <button><i class="material-icons">more_vert</i></button>
                    <ul>
                        <li><form action="tags.php?mode=edit" method="post">
                                <input type="hidden" name="id" value="<?=$tag['id']?>">
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


endif;