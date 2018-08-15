<?php

class html_tool {

    /**
     * Creates admin table frame.
     *
     * ## Config
     *
     * __String__ --_table_
     *
     * __String__ --*element_id*
     *
     * __Int__ --*max*
     *
     * __String__ *title*
     *
     * __String__ *source_url*
     *
     * __String__ *edit_url*
     *
     * __String__ *delete_url*
     *
     * __Int__ _limit_
     *
     * @param array $config
     *
     * @return string
     */
    static function table_frame($config) {
        $defaults = [
            'title' => 'List',
            'source_url' => $config['element_id'].".php?mode=getlist",
            'edit_url' => $config['element_id'].".php?mode=edit",
            'delete_url' => $config['element_id'].".php?mode=delete",
            'limit' => 10
        ];
        $config = array_merge($defaults, $config);

        return "
            <main>
                <div class='card'>
                    <div class='card__header'>
                        <ul class='header__row'>
                            <li><ul class='header__row-list--left'>
                                <li><h1>$config[title]</h1></li>
                            </ul></li>
                            <li><ul class='header__row-list--right'>
                                <li><a href='$config[edit_url]' class='button__icon--primary'><i class='material-icons'>add</i></a></li>
                            </ul></li>
                        </ul>
                        <ul class='header__row--contextual' id='$config[element_id]-contextual'>
                            <li><ul class='header__row-list--left'>
                                <li class='contextualAmount'></li>
                            </ul></li>
                            <li><ul class='header__row-list--right'>
                                <li><button id='$config[element_id]-delete' class='button__icon--dark'>
                                    <i class='material-icons'>delete</i>
                                </button></li>
                            </ul></li>
                        </ul>
                    </div>
                    <table
                        id='$config[element_id]'
                        class='table'
                        data-contextual='$config[element_id]-contextual'
                    ></table>
                    <div class='table-footer'>
                        <button class='table-footer__next' id='$config[element_id]-next'><i class='material-icons'>chevron_right</i></button>
                        <button class='table-footer__next' id='$config[element_id]-prev'><i class='material-icons'>chevron_left</i></button>
                        <p class='table-footer__status' id='users-status'></p>
                    </div>
                </div>
            </main>
            <script src='assets/script.js'></script>
            <script>
                new Table('users', {
                  source: {
                    url: '$config[source_url]',
                    limit: $config[limit],
                    max: $config[max]
                  }
                });
                let deleteDialog = new Dialog({
                    title: 'Caution',
                    body: 'Are you sure you want to delete the selected rows?',
                    actions: {
                      confirm: 'TableActions.delete(\'$config[delete_url]\', \'$config[element_id]\');',
                      cancel: 'Dialog.close(deleteDialog.dialog)'
                    }
                });
                let deleteButton = document.getElementById('$config[element_id]-delete');
                deleteButton.addEventListener('click', function() {
                  Dialog.open(deleteDialog.dialog);
                });
            </script>
        ";
    }

}