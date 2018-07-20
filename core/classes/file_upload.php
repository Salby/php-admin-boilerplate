<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 7/17/18
 * Time: 11:02 AM
 */

class file_upload {

    public function image($conf) {

        $defaults = array(
            'format' => 'jpeg',
            'quality' => 80,
            'max_size' => 100
        );
        $conf = array_merge($defaults, $conf);

        $destination_folder = $conf['destination'];
        $name = $conf['name'];
        $format = $conf['format'];
        $quality = $conf['quality'];
        $max_size = $conf['max_size'];

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
                    save_image($tmp, $destination_folder.$name.'.'.$format, array_search($mime, $allowed_mimes), $quality, $max_size);
                    return $destination_folder.$name.'.'.$format;
                } else { // Mime isn't allowed.
                    echo "This isn't the right format, sorry :/";
                }

            } else { // File is not image.
                echo "Sorry, this isn't right :(";
            }
        }
    }
}

function save_image($source_image, $destination, $mime, $quality, $max_size) {

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
    switch (strtoupper($mime)) {
        case 'GIF':
            //$image = imagecreatefromgif($source_image);
            imagegif($image, $destination);
            break;
        case 'PNG':
            //$image = imagecreatefrompng($source_image);
            imagepng($image, $destination, $quality);
            break;
        default:
        case 'JPEG':
            //$image = imagecreatefromjpeg($source_image);
            imagejpeg($image, $destination, $quality);
            break;
    }
}