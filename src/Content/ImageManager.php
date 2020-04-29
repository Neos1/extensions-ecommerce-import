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
     * Path to cookie file or false if cookies shouldn't be used
     * @var string|false
     */
    protected $cookieFile = false;

    /**
     * Download timeout in seconds, default is 60 seconds
     * @var integer
     */
    protected $timeout = 60;

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
     * @param array $allowed_extensions
     * @param array $options
     */
    public function __construct($base_dir, $allowed_extensions = [], $options = [])
    {
        if (!is_dir($base_dir) || !is_writable($base_dir)) {
            throw new RuntimeException('Base directory is not writable: ' . $base_dir);
        }
        $this->baseDir = rtrim($base_dir, '/') . '/';
        if (count($allowed_extensions) > 0) {
            $this->allowedExtensions = $allowed_extensions;
        }
        if (isset($options['store_cookies'])) {
            $this->cookieFile = tempnam($base_dir, 'cookie_');
        }
        if (isset($options['timeout']) && is_integer($options['timeout'])) {
            $this->timeout = $options['timeout'];
        }
    }

    /**
     * Removes cookie file on destruct
     */
    public function __destruct()
    {
        if ($this->cookieFile !== false) {
            unlink($this->cookieFile);
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
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt(
            $ch,
            CURLOPT_USERAGENT,
            'EcommerceImportBot/0.0.5 (https://github.com/Neos1/extensions-ecommerce-import)'
        );
        if ($this->cookieFile !== false) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEJAR, 1);
        }
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
