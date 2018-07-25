<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 7/21/18
 * Time: 2:11 PM
 */

class User extends file_upload {

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

    public function get_list($config = array()) {
        $sql = "SELECT user.*,
                  r.name as role
                  FROM user
                JOIN role r on user.role = r.id
                WHERE
                  user.deleted = 0";

        if (!empty($config)) {
            $sql .= " LIMIT $config[limit] OFFSET $config[offset]";
        }

        return $this -> db -> fetch_array($sql);
    }
    public function get_item($id, $return_row = false) {
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

            if ($return_row) {
                return $row;
            } else {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->password = $row['password'];
                $this->email = $row['email'];
                $this->avatar = $row['avatar'];
                $this->role = $row['role'];
                $this->suspended = $row['suspended'];
                $this->deleted = $row['deleted'];
                $this->salt = $row['salt'];
            }
        }
    }

    public function save($destination, $image_type = 'jpeg') {

        $image_name = strtolower(str_replace(' ', '_', $this->username)) . '_avatar';

        if ($this -> id) { // Update:

            // Find previous avatar and compare.
            $user = $this -> get_item($this->id, true);
            $original_avatar = $user['avatar'];

            // Replace image_url if avatar is set.
            $avatar = !$_FILES['user_avatar']['tmp_name']
                ? $original_avatar
                : parent::image([
                    'destination' => $destination,
                    'name' => $image_name,
                    'type' => $image_type
                ]);

            // Set parameters.
            $params = array(
                $this -> username,
                $this -> email,
                $this -> address,
                $avatar,
                $this -> role,
                $this -> suspended,
                $this -> id
            );
            $sql = "UPDATE user SET
                      username = ?,
                      email = ?,
                      address = ?,
                      avatar = ?,
                      role = ?,
                      suspended = ?
                    WHERE
                      id = ?";
            $this -> db -> query($sql, $params);

        } else { // Create:

            // Upload image.
            $image_url = parent::image([
                'destination' => $destination,
                'name' => $image_name,
                'type' => $image_type
            ]);
            // Set image_url to blank string if no image was uploaded.
            if (!$image_url) {
                $image_url = '';
            }

            // Hash password
            /** @noinspection PhpUnhandledExceptionInspection */
            $salt_byte = random_bytes(15);
            $salt_string = bin2hex($salt_byte);
            $password_hashed = password_hash($this->password . $salt_string, PASSWORD_BCRYPT);

            $params = array(
                $this -> username,
                $password_hashed,
                $this -> email,
                $this -> address,
                $image_url,
                $this -> role,
                $salt_string
            );
            $sql = "INSERT INTO 
                      user 
                        (username, password, email, address, avatar, role, salt) 
                    VALUES 
                      (?, ?, ?, ?, ?, ?, ?)";
            $this -> db -> query($sql, $params);
        }
    }

    public function delete($rows, $permanent = false) {

        $update = implode(',',$rows);

        if ($permanent) { // Permanent - REMOVES FROM TABLE:
            $sql = "DELETE
                      FROM user
                    WHERE
                      id IN ($update)";
        } else { // Soft delete - Sets deleted column to 1:
            $sql = "UPDATE user
                    SET deleted = 1
                    WHERE
                      id IN ($update)";
        }
        $this -> db -> query($sql);
    }
}