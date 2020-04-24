<?php

namespace Whitebox\EcommerceImport\Test;

use PHPUnit\Framework\TestCase;
use Whitebox\EcommerceImport\Parser\YMLParser;
use Whitebox\EcommerceImport\Schema;
use Whitebox\EcommerceImport\Schema\Param;

/**
 * @covers Whitebox\EcommerceImport\Schema
 */
class SchemaTest extends TestCase
{
    public function testCreateFromYaml()
    {
        $raw = <<<EOD
parser: yml
entities:
  offer:
    name:
      alias: name-ru_RU
    description:
    price:
      required: true
      type: float
    ean:
      parser_options: []
EOD;
        $schema = Schema::createFromYaml($raw);
        $this->assertInstanceOf(Schema::class, $schema);
        $this->assertInstanceOf(YMLParser::class, $schema->getParser());
        $this->assertCount(1, $schema->getEntities());
        $offer = $schema->getEntities()[0];
        $this->assertEquals('offer', $offer->getName());
        $this->assertEquals(
            [
                'name',
                'description',
                'price',
                'ean'
            ],
            array_keys($offer->getParams())
        );
        $this->assertEquals('name-ru_RU', $offer->getParam('name')->getAlias());
        $this->assertFalse($offer->getParam('description')->isRequired());
        $this->assertTrue($offer->getParam('price')->isRequired());
        $this->assertEquals('float', $offer->getParam('price')->getType());
        $this->assertEquals([], $offer->getParam('ean')->getParserOptions());
    }
}
