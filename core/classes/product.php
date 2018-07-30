<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 7/30/18
 * Time: 12:41 PM
 */

class Product extends file_upload {

    public $id;
    public $name;
    public $description;
    public $summary;
    public $image;
    public $price;
    public $is_private;
    public $deleted;

    public $category;

    protected $db;

    public function __construct() {
        global $db;
        $this -> db = $db;
    }

    public function get_list($config = array()) {

        $params = array();

        if (isset($config['private'])) {
            $params['private'] = $config['private']
                ? 1
                : 0;
            $private = " AND is_private = ?";
        } else {
            $private = "";
        }

        $search = isset($config['query'])
            ? util::search([ 'query' => $config['query'] ])
            : "";

        $sql = "SELECT product.*,
                  GROUP_CONCAT(category.name SEPARATOR '-----') AS category
                  FROM product
                JOIN product_category
                  ON product.id = product_category.product_id
                JOIN category
                  ON product_category.category_id = category.id
                WHERE
                  product.deleted = 0
                  $private
                  $search
                GROUP BY product.id";

        if (isset($config['limit'])) {
            $sql .= " LIMIT $config[limit]";
        }
        if (isset($config['offset'])) {
            $sql .= " OFFSET $config[offset]";
        }

        $row = $this -> db -> fetch_array($sql, $params);

        foreach ($row as $r) {
            util::merge_concat([
                'target' => $r,
                'source' => 'category'
            ]);
        }

        return $row;
    }
    public function get_item($id) {

        $params = array($id);
        $sql = "SELECT product.*,
                  category.name
                  FROM product
                JOIN product_category
                  ON product.id = product_category.product_id
                JOIN category
                  ON product_category.category_id = category.id
                WHERE
                  product.deleted = 0
                  AND 
                  product.id = ?";
        $row = $this -> db -> fetch_array($sql, $params);

        return call_user_func_array('array_merge', $row);
    }

    public function save($destination, $type = 'jpeg') {

        $image_name = strtolower(str_replace(' ', '_', $this->name)) . '_product';
        $image_config = array(
            'destination' => $destination,
            'name' => $image_name,
            'type' => 'jpeg'
        );

        if ($this -> id) { // Update

            // Get original row.
            $original = $this -> get_item($this->id);

            // Update image if set.
            $image_url = !empty($this->image)
                ? parent::image($image_config)
                : $original['image'];

            // Set parameters and SQL query.
            $params = array(
                $this -> name,
                $this -> description,
                $this -> summary,
                $image_url,
                $this -> price,
                $this -> is_private,
                $this -> id
            );
            $sql = "UPDATE product
                      SET
                        name = ?,
                        description = ?,
                        summary = ?,
                        image = ?,
                        price = ?,
                        is_private = ?
                    WHERE
                      id = ?";

            // Update.
            $this -> db -> query($sql, $params);

        } else { // Create:

            // Save image.
            $image_url = parent::image($image_config);

            // Set parameters and SQL query.
            $params = array(
                $this -> name,
                $this -> description,
                $this -> summary,
                $image_url,
                $this -> price,
                $this -> is_private
            );
            $sql = "INSERT INTO
                      product
                        (name, description, summary, image, price, is_private)
                    VALUES
                      (?, ?, ?, ?, ?, ?)";

            // Insert.
            $this -> db -> query($sql, $params);

            if (!empty($this->category)) {
                // Build parameters and SQL query for categories.
                $product_id = $this -> db -> getinsertid();
                $params = array();
                $markers = array();
                foreach ($this->category as $category) {
                    if ($category != 0) {
                        $params[] =  $product_id;
                        $params[] = $category;
                        $markers[] = "(?, ?)";
                    }
                }
                $markers  = implode(', ', $markers);
                $sql = "INSERT INTO
                          product_category
                            (product_id, category_id) 
                        VALUES
                          $markers";

                // Insert.
                $this -> db -> query($sql, $params);
            }

        }

    }

    public function delete($rows, $permanent = false) {

        $rows = implode(', ', $rows);

        if ($permanent) { // REMOVES from table.

            // Set SQL query.
            $sql = "DELETE
                      FROM product
                    WHERE
                      id IN ($rows)";

        } else { // Sets deleted as 0.

            // Set SQL query.
            $sql = "UPDATE product
                      SET
                        deleted = 1
                    WHERE
                      id IN ($rows)";

        }

        // Execute query.
        $this -> db -> query($sql);

    }

}