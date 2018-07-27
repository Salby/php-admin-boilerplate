<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 7/26/18
 * Time: 10:59 AM
 */

class Blog extends file_upload {

    public $id;
    public $author;
    public $thumbnail;
    public $title;
    public $content;
    public $created;
    public $is_private;
    public $deleted;

    public $tags;

    protected $db;

    public function __construct() {
        global $db;
        $this -> db = $db;
    }

    public function get_list($config = array()) {
        $params = array();
        if (isset($config['private'])) {
            $params['private'] = $config['private'];
        }
        $private = isset($config['private'])
            ? ' AND is_private = ?'
            : '';
        $sql = "SELECT blog.*,
                  user.name AS author,
                  GROUP_CONCAT(tag.name SEPARATOR '-----') AS tag
                  FROM blog
                JOIN user
                  ON blog.author = user.id
                JOIN blog_tag
                  ON blog.id = blog_tag.blog_id
                JOIN tag
                  ON blog_tag.tag_id = tag.id
                WHERE
                  blog.deleted = 0
                  $private
                GROUP BY blog.id";
        if (isset($config['limit']) && isset($config['offset'])) {
            $sql .= " LIMIT $config[limit] OFFSET $config[offset]";
        }
        $row = $this -> db -> fetch_array($sql, $params);
        foreach ($row as $r) {
            util::merge_concat([
                'target' => $r,
                'source' => 'tag'
            ]);
        }
        return $row;
    }
    public function get_item($id, $config = array()) {
        $params = array(
            $id
        );
        if (isset($config['private'])) {
            $params['private'] = $config['private'];
        }
        $private = isset($config['private'])
            ? ' AND is_private = ?'
            : '';
        $sql = "SELECT blog.*,
                  user.name,
                  GROUP_CONCAT(tag.name SEPARATOR '-----') AS tag
                  FROM blog
                JOIN user
                  ON blog.author = user.id
                JOIN blog_tag
                  ON blog.id = blog_tag.blog_id
                JOIN tag
                  ON blog_tag.tag_id = tag.id
                WHERE
                  blog.deleted = 0
                  AND 
                  blog.id = ?
                  $private
                GROUP BY blog.id";
        $row = $this -> db -> fetch_array($sql, $params);
        $row = call_user_func_array('array_merge', $row);
        util::merge_concat([
            'target' => $row,
            'source' => 'tag'
        ]);
        return $row;
    }

    public function save($destination, $type = 'jpeg') {

        $image_name = strtolower(str_replace(' ', '_', $this -> title)) . '_thumbnail';
        $image_config = array(
            'destination' => $destination,
            'name' => $image_name,
            'type' => $type
        );

        if ($this -> id) { // Update:

            // Upload new image if set.
            $original = $this -> get_item($this->id);
            $image_url = !empty($this->thumbnail)
                ? parent::image($image_config)
                : $original['thumbnail'];

            // Define blog parameters and SQL query.
            $params = array(
                $this -> author,
                $image_url,
                $this -> title,
                $this -> content,
                $this -> is_private,
                $this -> id
            );
            $sql = "UPDATE blog SET
                      author = ?,
                      thumbnail = ?,
                      title = ?,
                      content = ?,
                      is_private = ?
                    WHERE
                      id = ?";

            // Update.
            $this -> db -> query($sql, $params);

            // TODO: Build tag parameters and SQL query.
            // TODO: Delete old tag references and insert new.

        } else { // Create:

            // Upload image.
            $image_url = parent::image($image_config);

            // Define blog parameters and SQL query.
            $params = array(
                $this -> author,
                $image_url,
                $this -> title,
                $this -> content,
                $this -> is_private
            );
            $sql = "INSERT INTO
                      blog
                        (author, thumbnail, title, content, is_private)
                    VALUES
                      (?, ?, ?, ?, ?)";

            // Insert.
            $this -> db -> query($sql, $params);

            // Build tag parameters and SQL query.
            if (!empty($this->tags)) {
                $blog_id = $this->db->getinsertid();
                $params = array();
                $markers = array();
                foreach ($this->tags as $tag) {
                    if ($tag != 0) {
                        $params[] = $blog_id;
                        $params[] = $tag;
                        $markers[] = "(?, ?)";
                    }
                }
                $markers = implode(', ', $markers);
                $sql = "INSERT INTO
                      blog_tag
                        (blog_id, tag_id)
                    VALUES
                      $markers";

                // Insert.
                $this->db->query($sql, $params);
            }
        }

    }

    public function delete($rows, $permanent = false) {

        $rows = implode(', ', $rows);

        if ($permanent) { // REMOVES row(s) from database.

            $sql = "DELETE
                      FROM blog
                    WHERE
                      id IN ($rows)";

        } else { // Sets deleted to 1.

            $sql = "UPDATE blog
                    SET
                      deleted = 1
                    WHERE
                      id IN ($rows)";

        }

        // Execute query.
        $this -> db -> query($sql);

    }

}