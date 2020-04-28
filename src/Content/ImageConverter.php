<?php

namespace Whitebox\EcommerceImport\Content;

class ImageConverter
{
    /**
     * @param string $src
     * @param string $dest
     * @param integer $quality
     * @return boolean
     */
    public static function copyImage($src, $dest, $quality = 85)
    {
        $type = exif_imagetype($src);
        switch ($type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($src);
                imageinterlace($image, true);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($src);
                break;
        }
        $result = isset($image) ? static::createImageFromResource($image, $type, $dest, $quality) : false;
        imagedestroy($image);
        return $result;
    }

    /**
     * @param string $src
     * @param string $dest
     * @param integer $width
     * @param integer $quality
     * @return boolean
     */
    public static function makeThumb($src, $dest, $width, $quality = 85)
    {
        $result = false;
        $type = exif_imagetype($src);
        switch ($type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($src);
                imageinterlace($image, true);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($src);
                break;
            default:
                return false;
        }
        $original_height = imagesy($image);
        $original_width = imagesx($image);
        $height = round($original_height * $width / $original_width);
        $thumb = imagecreatetruecolor($width, $height);
        if (imagecopyresampled($thumb, $image, 0, 0, 0, 0, $width, $height, $original_width, $original_height)) {
            $result = isset($image) ? static::createImageFromResource($thumb, $type, $dest, $quality) : false;
        }
        imagedestroy($image);
        imagedestroy($thumb);
        return $result;
    }

    /**
     * @param resource $image
     * @param integer $type
     * @param string $dest
     * @param integer $quality
     * @return void
     */
    public static function createImageFromResource($image, $type, $dest, $quality = 85)
    {
        switch ($type) {
            case IMAGETYPE_JPEG:
                return imagejpeg($image, $dest, $quality);
            case IMAGETYPE_PNG:
                $compression = ceil($quality * 9 / 100);
                return imagepng($image, $dest, $compression);
            default:
                return false;
        }
    }
}
