<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 7/17/18
 * Time: 11:02 AM
 */

class file_upload {

    public $allowed_mimes = array(
        'image' => [
            'png' => 'image/png',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml'
        ],
    );

    public function image($conf) {

        $defaults = array(
            'type' => 'jpeg',
            'quality' => 80,
            'max_dimension' => 1920
        );
        $conf = array_merge($defaults, $conf);

        $destination_folder = $conf['destination'];
        $name = $conf['name'];
        $type = $conf['type'];
        $quality = $conf['quality'];
        $max_dimension = $conf['max_dimension'];

        $allowed_mimes = array(
            'png' => 'image/png',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
        );

        foreach ($_FILES as $image) {
            $tmp = $image['tmp_name'];

            $validate = getimagesize($tmp);
            $mime = $validate['mime'];
            if ($validate) { // File is image.

                if (in_array($mime, $allowed_mimes)) { // Mime is allowed.
                    $name = $this -> file_exists($destination_folder.$name.'.'.$type);
                    save_image($tmp, $name, $type, $quality, $max_dimension);
                    return $name;
                } else { // Mime isn't allowed.
                    return false;
                }

            } else { // File is not image.
                return false;
            }
        }
    }

    public function file_exists($name) {
        $file = $name;
        $exists = file_exists($file);

        $i = 0;
        while ($exists) {
            $i++;
            $exploded = explode('.', $name);
            $new_file = $exploded[0] . '_' . $i . '.' . $exploded[1];
            if (!file_exists($new_file)) {
                return $new_file;
                break;
            }
        }
    }
}

function save_image($source_image, $destination, $type, $quality, $max_size) {

    $size = getimagesize($source_image);
    if ($size[0] > $max_size || $size[1] > $max_size) {
        $ratio = $size[0] / $size[1];
        if ($ratio > 1) {
            $width = $max_size;
            $height = $max_size / $ratio;
        } else {
            $width = $max_size * $ratio;
            $height = $max_size;
        }
    } else {
        $width = $size[0];
        $height = $size[1];
    }
    $src = imagecreatefromstring(file_get_contents($source_image));
    $image = imagecreatetruecolor($width, $height);
    imagecopyresampled($image, $src, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
    imagedestroy($src);
    switch (strtoupper($type)) {
        case 'SVG':
            move_uploaded_file($source_image, $destination);
            break;
        case 'GIF':
            imagegif($image, $destination);
            break;
        case 'PNG':
            imagepng($image, $destination, $quality);
            break;
        default:
        case 'JPEG':
            imagejpeg($image, $destination, $quality);
            break;
    }
}