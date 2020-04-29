<?php

namespace Whitebox\EcommerceImport\Test\Content;

use RuntimeException;
use PHPUnit\Framework\TestCase;
use Whitebox\EcommerceImport\Content\ImageManager;

/**
 * @covers Whitebox\EcommerceImport\Content\ImageManager
 */
class ImageManagerTest extends TestCase
{
    const BASE_DIR = __DIR__ . '/tmp';

    /**
     * @var ImageManager
     */
    protected static $manager;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::$manager = new ImageManager(
            static::BASE_DIR,
            [],
            ['store_cookies' => true, 'timeout' => 10]
        );
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        foreach (glob(static::BASE_DIR . '/*') as $filename) {
            unlink($filename);
        }
        foreach (glob(static::BASE_DIR . '/**/*') as $filename) {
            unlink($filename);
        }
    }

    public function testConstructorFailsWithNotWritableBaseDir()
    {
        $this->expectException(RuntimeException::class);
        new ImageManager(__DIR__ . '/non-existing');
    }

    public function testGetImageName()
    {
        $this->assertEquals(
            'image.jpg',
            static::$manager->getImageName('http://example.org/image.jpg?v123')
        );
    }

    public function testDownload()
    {
        $filename = static::$manager->download(
            'https://whitebox1.io/assets/images/logo-icon.png'
        );
        $this->assertEquals(static::BASE_DIR . '/logo-icon.png', $filename);
    }

    public function testDownloadWithCustomAllowedExtensions()
    {
        $manager = new ImageManager(static::BASE_DIR, ['jpg'], ['store_cookies' => 1, 'timeout' => 10]);
        $filename = $manager->download(
            'https://whitebox1.io/assets/images/logo-icon.png'
        );
        $this->assertNull($filename);
    }

    public function testDownloadToCustomDistFailsWithDisabledDirAutoCreation()
    {
        $manager = clone static::$manager;
        $manager->createDistDir = false;
        $filename = $manager->download(
            'https://whitebox1.io/assets/images/logo-icon.png',
            'non-existing'
        );
        $this->assertNull($filename);
    }

    public function testDownloadToCustomDist()
    {
        if (is_dir(static::BASE_DIR . '/custom')) {
            rmdir(static::BASE_DIR . '/custom');
        }
        $filename = static::$manager->download(
            'https://whitebox1.io/assets/images/logo-icon.png',
            'custom'
        );
        $this->assertEquals(static::BASE_DIR . '/custom/logo-icon.png', $filename);
    }
}
