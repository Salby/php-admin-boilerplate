<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 7/21/18
 * Time: 2:11 PM
 */

class User extends file_upload {

    public $id;
    public $name;
    public $password;
    public $email;
    public $address;
    public $city;
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

        $search = isset($config['query'])
            ? util::search([
                'target' => 'user.name',
                'query' => $config['query']
            ])
            : '';

        $sql = "SELECT user.*,
                  r.name AS role,
                  c.name AS city_name
                  FROM user
                JOIN role r ON user.role = r.id
                JOIN city c ON user.city = c.id
                WHERE
                  user.deleted = 0
                  $search";

        if (isset($config['limit']) && isset($config['offset'])) {
            $sql .= " LIMIT $config[limit] OFFSET $config[offset]";
        }

        return $this -> db -> fetch_array($sql);
    }
    public function get_item($id) {
        $params = array($id);
        $sql = "SELECT user.*,
                  role.name AS role,
                  city.name AS city_name
                  FROM user
                JOIN role
                  ON user.role = role.id
                JOIN city
                  ON user.city = city.id
                WHERE
                  user.id = ?";
        $row = $this -> db -> fetch_array($sql, $params);
        return call_user_func_array('array_merge', $row);
    }

    public function save($destination, $image_type = 'jpeg') {

        $image_name = strtolower(str_replace(' ', '_', $this->name)) . '_avatar';
        $image_config = array(
            'destination' => $destination,
            'name' => $image_name,
            'type' => $image_type,
            'max_dimension' => 512
        );

        if ($this -> id) { // Update:

            // Find previous avatar and compare.
            $user = $this -> get_item($this->id);
            $original_avatar = $user['avatar'];
            // Replace image_url if avatar is set.
            $avatar = !empty($_FILES)
                ? parent::image($image_config)
                : $original_avatar;

            // Set parameters.
            $params = array(
                $this -> name,
                $this -> email,
                $this -> address,
                $this -> city,
                $avatar,
                $this -> role,
                $this -> suspended,
                $this -> id
            );
            $sql = "UPDATE user SET
                      name = ?,
                      email = ?,
                      address = ?,
                      city = ?,
                      avatar = ?,
                      role = ?,
                      suspended = ?
                    WHERE
                      id = ?";
            $this -> db -> query($sql, $params);

        } else { // Create:

            // Set image url and upload.
            $avatar = parent::image($image_config);

            // Hash password
            /** @noinspection PhpUnhandledExceptionInspection */
            $salt_byte = random_bytes(15);
            $salt_string = bin2hex($salt_byte);
            $password_hashed = password_hash($this->password . $salt_string, PASSWORD_BCRYPT);

            $params = array(
                $this -> name,
                $password_hashed,
                $this -> email,
                $this -> address,
                $this -> city,
                $avatar,
                $this -> role,
                $salt_string
            );
            $sql = "INSERT INTO 
                      user 
                        (name, password, email, address, city, avatar, role, salt) 
                    VALUES 
                      (?, ?, ?, ?, ?, ?, ?, ?)";
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
            $sql = "UPDATE user SET
                      deleted = 1
                    WHERE
                      id IN ($update)";
        }
        $this -> db -> query($sql);
    }
}