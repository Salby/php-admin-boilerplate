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
        ?>

    <main>
        <div class="card">
            <div class="card__header">
                <ul class="header__row">
                    <li><ul class="header__row-list--left">
                            <li><h1>List</h1></li>
                        </ul></li>
                    <li><ul class="header__row-list--right">
                            <li><a href="posts.php?mode=edit" class="button__icon--primary"><i class="material-icons">add</i></a></li>
                        </ul></li>
                </ul>
                <ul class="header__row--contextual" id="posts-contextual">
                    <li><ul class="header__row-list--left">
                            <li class="contextualAmount"></li>
                        </ul></li>
                    <li><ul class="header__row-list--right">
                            <li><button id="posts-delete" class='button__icon--dark'>
                                    <i class='material-icons'>delete</i>
                                </button></li>
                        </ul></li>
                </ul>
            </div>
            <table class="table" id="posts" data-contextual="posts-contextual">
            </table>
            <div class="table-footer">
                <button class="table-footer__next" id="posts-next"><i class="material-icons">chevron_right</i></button>
                <button class="table-footer__prev" id="posts-prev"><i class="material-icons">chevron_left</i></button>
                <p class="table-footer__status" id="posts-status"></p>
            </div>
        </div>
    </main>

    <script src="assets/script.js"></script>
    <script>
        new Table('posts', {
          source: {
            url: 'posts.php?mode=getlist',
            limit: 10,
            max: <?=$db->get_amount('blog')?>
          }
        });
        let deleteDialog = new Dialog({
          title: 'Caution.',
          message: 'Are you sure you want to delete the selected post(s)?',
          actions: {
            confirm: 'TableActions.delete(\'posts.php?mode=delete\', \'posts\'); Dialog.close(deleteDialog.dialog)',
            cancel: 'Dialog.close(deleteDialog.dialog)'
          }
        });
        document.getElementById('posts-delete').addEventListener('click', () => Dialog.open(deleteDialog.dialog));
    </script>

        <?php
        break;


    case 'EDIT':

        $labels = array(
            'is_private' => 'Private',
            'tag_tag_id' => 'Tag'
        );
        $exceptions = array(
            'deleted' => ''
        );
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $pageTitle = "Posts · Edit";
            $form_source = $post -> get_item($_POST['id']);
            $labels['_form_title'] = "Edit post";
        } else {
            $pageTitle = "Posts · Create";
            $form_source = [];
            $labels['_form_title'] = "New post";
        }
        require_once('incl/header.php');
        $form = new form_builder();
        ?>
    <main>
        <div class="card">
            <?php
            $form -> build([
                'table_name' => 'blog',
                'action' => 'posts.php?mode=save',
                'method' => 'post',
                'source' => $form_source
            ], $labels, $exceptions);
            ?>
        </div>
    </main>
    <script src="assets/script.js"></script>
    <script>
        new Form('blog');
    </script>
        <?php
        break;


    case 'SAVE':

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $post -> id = isset($_POST['id']) && !empty($_POST['id'])
            ? $_POST['id']
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