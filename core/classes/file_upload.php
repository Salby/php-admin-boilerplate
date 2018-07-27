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

    public function image($config) {
        // Define defaults for configuration.
        $defaults = array(
            'type' => 'jpeg',
            'quality' => 80,
            'max_dimension' => 1920,
            'placeholder' => 'https://placem.at/places?txt=Image+not+found'
        );
        // Merge defaults with user-defined config.
        $config = array_merge($defaults, $config);

        if (!empty($_FILES)) {
            foreach ($_FILES as $image) {
                $tmp = $image['tmp_name']; // Get tmp name.
                $info = getimagesize($tmp); // Get file info.
                $mime = $info['mime']; // Get file mime.
                if (in_array($mime, $this->allowed_mimes['image'])) { // Check if mime is allowed.
                    $file = $this->file_exists($config['destination'] . $config['name'] . '.' . $config['type']); // Check if file already exists.
                    save_image($tmp, $file.'.'.$config['type'], $config['type'], $config['quality'], $config['max_dimension']); // Save image.
                    return $file . '.' . $config['type']; // Return image url.
                } else {
                    return $config['placeholder'];
                }
            }
        } else {
            return $config['placeholder'];
        }
    }

    public function file_exists($name) {
        $file = $name;
        $exists = file_exists($file);

        $i = 0;
        if ($exists) {
            while ($exists) {
                $i++;
                $exploded = explode('.', $name);
                $new_file = $exploded[0] . '_' . $i . '.' . $exploded[1];
                if (!file_exists($new_file)) {
                    return explode('.', $new_file)[0];
                    break;
                }
            }
        } else {
            return explode('.', $file)[0];
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
            $quality = ($quality - 100) / 11.111111;
            $quality = round(abs($quality));
            imagepng($image, $destination, $quality);
            break;
        default:
        case 'JPEG':
            imagejpeg($image, $destination, $quality);
            break;
    }
}