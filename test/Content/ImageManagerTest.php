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
    protected $manager;

    protected function setUp()
    {
        parent::setUp();
        $this->manager = new ImageManager(static::BASE_DIR);
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
            $this->manager->getImageName('http://example.org/image.jpg?v123')
        );
    }

    public function testDownload()
    {
        $filename = $this->manager->download(
            'https://whitebox1.io/assets/images/logo-icon.png'
        );
        $this->assertEquals(static::BASE_DIR . '/logo-icon.png', $filename);
    }

    public function testDownloadWithCustomAllowedExtensions()
    {
        $manager = new ImageManager(static::BASE_DIR, ['jpg']);
        $filename = $manager->download(
            'https://whitebox1.io/assets/images/logo-icon.png'
        );
        $this->assertNull($filename);
    }

    public function testDownloadToCustomDistFailsWithDisabledDirAutoCreation()
    {
        $this->manager->createDistDir = false;
        $filename = $this->manager->download(
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
        $filename = $this->manager->download(
            'https://whitebox1.io/assets/images/logo-icon.png',
            'custom'
        );
        $this->assertEquals(static::BASE_DIR . '/custom/logo-icon.png', $filename);
    }
}
