<?php

namespace Whitebox\EcommerceImport\Content;

use RuntimeException;

class ImageManager
{
    /**
     * @var string
     */
    protected $baseDir;

    /**
     * @var array
     */
    protected $allowedExtensions = ['jpeg', 'jpg', 'png'];

    /**
     * @var boolean
     */
    public $createDistDir = true;

    /**
     * @param string $base_dir
     */
    public function __construct($base_dir, $allowed_extensions = [])
    {
        if (!is_dir($base_dir) || !is_writable($base_dir)) {
            throw new RuntimeException('Base directory is not writable: ' . $base_dir);
        }
        $this->baseDir = rtrim($base_dir, '/') . '/';
        if (count($allowed_extensions) > 0) {
            $this->allowedExtensions = $allowed_extensions;
        }
    }

    /**
     * @param string $link
     * @param string $dist
     * @return string|null Filename or null if file wasn't downloaded
     */
    public function download($link, $dist = '')
    {
        $image_name = $this->getImageName($link);
        if (!$this->isAllowedImage($image_name)) {
            return null;
        }
        $image_content = $this->getImageContent($link);
        if ($this->isValidImage($image_name, $image_content)) {
            $filename = $this->baseDir;
            if ($dist) {
                $filename .= rtrim($dist, '/') . '/';
            }
            if ($this->createDistDir && !file_exists($filename)) {
                mkdir($filename, 0755, true);
            }
            $filename .= $image_name;
            if (file_put_contents($filename, $image_content)) {
                return $filename;
            }
        }
        return null;
    }

    /**
     * getImageName returns image name from the link
     * @param string $link
     * @return string
     */
    public function getImageName($link)
    {
        $image_name = basename($link);
        $query_position = strpos($image_name, '?');
        if ($query_position !== false) {
            $image_name = substr($image_name, 0, $query_position);
        }
        return $image_name;
    }

    public function isAllowedImage($image_name)
    {
        $extension = pathinfo($image_name, PATHINFO_EXTENSION);
        return in_array($extension, $this->allowedExtensions);
    }

    /**
     * @param string $link
     * @return mixed
     */
    public function getImageContent($link)
    {
        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $image_content = curl_exec($ch);
        curl_close($ch);
        return $image_content;
    }

    /**
     * @param string $link
     * @param mixed $image_content
     * @return boolean
     */
    public function isValidImage($image_name, $image_content)
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $ftype = $finfo->buffer($image_content);
        $extension = pathinfo($image_name, PATHINFO_EXTENSION);
        if ($extension === 'jpg') {
            $extension = 'jpeg';
        }
        $mime_extension = substr(strstr($ftype, '/'), 1);
        return $mime_extension === $extension;
    }
}
