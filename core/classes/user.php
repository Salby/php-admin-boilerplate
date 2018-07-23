<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 7/21/18
 * Time: 2:11 PM
 */

class User {

    public $id;
    public $username;
    public $password;
    public $email;
    public $address;
    public $avatar;
    public $role;
    public $suspended;
    public $deleted;
    public $salt;

    protected $db;

    public function __construct() {
        global $db;
        $this -> db = $db;
    }

    public function getList() {
        $sql = "SELECT user.*,
                  r.name
                  FROM user
                JOIN role r on user.role = r.id
                WHERE
                  user.deleted = 0";
        return $this -> db -> fetch_array($sql);
    }
    public function getItem($id) {
        $params = array($id);
        $sql = "SELECT user.*,
                  role.name AS role
                  FROM user
                JOIN role
                  ON user.role = role.id
                WHERE
                  user.id = ?";
        $row = $this -> db -> fetch_array($sql, $params);
        if (!empty($row)) {
            $row = call_user_func_array('array_merge', $row);

            $this -> id = $row['id'];
            $this -> username = $row['username'];
            $this -> password = $row['password'];
            $this -> email = $row['email'];
            $this -> avatar = $row['avatar'];
            $this -> role = $row['role'];
            $this -> suspended = $row['suspended'];
            $this -> deleted = $row['deleted'];
            $this -> salt = $row['salt'];
        }
    }

    public function save($destination)
}