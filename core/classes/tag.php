<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 7/26/18
 * Time: 5:12 PM
 */

class Tag {

    public $id;
    public $name;
    public $deleted;

    protected $db;

    public function __construct() {
        global $db;
        $this -> db = $db;
    }

    public function get_list($config) {

        $search = isset($config['query'])
            ? util::search([ 'query' => $config['query'] ])
            : '';

        $sql = "SELECT *
                  FROM tag
                WHERE
                  deleted = 0
                  $search";

        if (isset($config['limit'])) {
            $sql .= " LIMIT $config[limit]";
        }
        if (isset($config['offset'])) {
            $sql .= " OFFSET $config[offset]";
        }

        return $this -> db -> fetch_array($sql);

    }
    public function get_item($id) {

        $params = array(
            $id
        );
        $sql = "SELECT *
                  FROM tag
                WHERE
                  deleted = 0
                  AND 
                  id = ?";

        $row = $this -> db -> fetch_array($sql, $params);
        return call_user_func_array('array_merge', $row);

    }

    public function save() {

        if ($this -> id) { // Update:

            // Define parameters and SQL query.
            $params = array(
                $this -> name,
                $this -> id
            );
            $sql = "UPDATE tag SET
                      name = ?
                    WHERE
                      id = ?";

            // Update.
            $this -> db -> query($sql, $params);

        } else { // Create:

            // Define parameters and SQL query.
            $params = array(
                $this -> name
            );
            $sql = "INSERT INTO
                      tag
                        (name)
                    VALUES
                      (?)";

            // Insert.
            $this -> db -> query($sql, $params);

        }

    }

    public function delete($selected, $permanent = false) {

        $update = implode(', ', $selected);

        if ($permanent) { // REMOVE from table.

            // Define SQL query.
            $sql = "DELETE
                      FROM tag
                    WHERE
                      id IN ($update)";

        } else { // Set deleted as 1.

            // Define SQL query.
            $sql = "UPDATE tag SET
                      deleted = 1
                    WHERE
                      id IN ($update)";

        }

        // Execute query.
        $this -> db -> query($sql);

    }

}