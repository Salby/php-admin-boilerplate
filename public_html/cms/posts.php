<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 7/26/18
 * Time: 1:35 PM
 */

require_once('incl/init.php');

if (
    $auth -> auth_role === 'admin'
    || $auth -> auth_role === 'editor'
) :

$post = new Blog();

$mode = isset($_REQUEST['mode']) && !empty($_REQUEST['mode']) ? $_REQUEST['mode'] : '';

switch (strtoupper($mode)) {


    default:
    case 'LIST':

        $pageTitle = 'Posts';
        require_once('incl/header.php');
        echo html_tool::table_frame([
            'table' => 'blog',
            'element_id' => 'posts',
            'max' => $db->get_amount('blog')
        ]);
        break;


    case 'EDIT':

        $labels = array(
            'is_private' => 'Private',
            'tag_id' => 'Tag'
        );
        $exceptions = array(
            'created' => '',
            'deleted' => ''
        );
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $pageTitle = "Posts · Edit";
            $form_source = $post -> get_item($_POST['id']);
            $labels['__form_title'] = "Edit post";
        } else {
            $pageTitle = "Posts · Create";
            $form_source = [];
            $labels['__form_title'] = "New post";
        }
        require_once('incl/header.php');
        $form = new form_builder();
        echo "<main><div class='card'>";
        echo $form -> build([
            'table' => 'blog',
            'action' => 'posts.php?mode=save',
            'method' => 'post',
            'source' => $form_source,
            'labels' => $labels,
            'exceptions' => $exceptions,
            'exclude' => [
                'user'
            ]
        ]);
        echo "</main></div><script src='assets/script.js'></script><script>new Form('blog')</script>";
        break;


    case 'SAVE':

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $post -> id = isset($_POST['blog_id']) && !empty($_POST['blog_id'])
            ? $_POST['blog_id']
            : 0;
        $post -> author = $_POST['blog_author'];
        $post -> thumbnail = $_FILES['blog_thumbnail'];
        $post -> title = $_POST['blog_title'];
        $post -> content = $_POST['blog_content'];
        $post -> is_private = $_POST['blog_is_private'];

        $post -> tags = $_POST['blog_tag_id'];

        $post -> save(DOCROOT.'/cms/upload/');
        header('Location: posts.php');

        break;


    case 'DELETE':

        $rows = filter_input(INPUT_POST, 'selected', FILTER_SANITIZE_STRING);
        if (substr_count($rows, ',')) {
            $rows = explode(',', $rows);
        } else {
            $rows = array($rows);
        }
        $post -> delete($rows);

        $str_end = count($rows) > 1
            ? ' posts'
            : ' post';
        echo "Deleted ".count($rows).$str_end;

        break;


    case 'GETLIST':

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $post_list = $post -> get_list([
            'limit' => $_POST['limit'],
            'offset' => $_POST['offset']
        ]);
        ?>
    <thead>
        <tr>
            <th class="select"><input type="checkbox" name="posts" class="table__checkbox master"></th>
            <th>Title</th>
            <th>Author</th>
            <th>Private</th>
            <th>Created</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($post_list as $post) :
        $private = $post['is_private']
            ? 'True'
            : 'False'; ?>
        <tr>
            <td class="select"><input type="checkbox" name="posts" value="<?=$post['id']?>" class="table__checkbox"></td>
            <td><?=$post['title']?></td>
            <td><?=$post['author']?></td>
            <td><?=$private?></td>
            <td><?=$post['created']?></td>
            <td align="right" class="options"><div class="menu--right">
                    <button><i class="material-icons">more_vert</i></button>
                    <ul>
                        <li><form action="posts.php?mode=edit" method="post">
                                <input type="hidden" name="id" value="<?=$post['id']?>">
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