<?php

namespace Whitebox\EcommerceImport\Test\Content;

use PHPUnit\Framework\TestCase;
use Whitebox\EcommerceImport\Content\ImageConverter;

/**
 * @covers Whitebox\EcommerceImport\Content\ImageConverter
 */
class ImageConverterTest extends TestCase
{

    protected $dest = __DIR__ . '/tmp';

    protected $src = __DIR__ . '/examples';

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        $files = glob($this->dest . '/*');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    /**
     * @return void
     */
    public function testCopyImagePng()
    {
        // Save copy with no compression!
        $this->assertTrue(ImageConverter::copyImage($this->src . '/test.png', $this->dest . '/copy.png', 0));
        // Original image is compressed, so copy should be larger
        $this->assertGreaterThan(filesize($this->src . '/test.png'), filesize($this->dest . '/copy.png'));
    }

    /**
     * @return void
     */
    public function testCopyImageJpeg()
    {
        // Save copy with lowest quality possible
        $this->assertTrue(ImageConverter::copyImage($this->src . '/test.jpg', $this->dest . '/copy.jpg', 0));
        // Original image should be larger
        $this->assertLessThan(filesize($this->src . '/test.jpg'), filesize($this->dest . '/copy.jpg'));
    }

    /**
     * @return void
     */
    public function testMakeThumbPng()
    {
        // Save copy with no compression!
        $this->assertTrue(ImageConverter::makeThumb($this->src . '/test.png', $this->dest . '/thumb.png', 150, 0));
        $size = getimagesize($this->src . '/test.png');
        $thumbsize = getimagesize($this->dest . '/thumb.png');
        $this->assertLessThan($size[0], $thumbsize[0]);
        $this->assertLessThan($size[1], $thumbsize[1]);
    }

    /**
     * @return void
     */
    public function testMakeThumbJpg()
    {
        // Save copy with no compression!
        $this->assertTrue(ImageConverter::makeThumb($this->src . '/test.jpg', $this->dest . '/thumb.jpg', 150, 0));
        $size = getimagesize($this->src . '/test.jpg');
        $thumbsize = getimagesize($this->dest . '/thumb.jpg');
        $this->assertLessThan($size[0], $thumbsize[0]);
        $this->assertLessThan($size[1], $thumbsize[1]);
    }
}
