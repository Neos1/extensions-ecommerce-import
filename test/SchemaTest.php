<?php

namespace Whitebox\EcommerceImport\Test;

use PHPUnit\Framework\TestCase;
use Whitebox\EcommerceImport\Parser\YMLParser;
use Whitebox\EcommerceImport\Schema;
use Whitebox\EcommerceImport\Schema\Param;

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
            array_map(function (Param $key) {
                return $key->getName();
            }, $offer->getParams())
        );
        $this->assertEquals('name-ru_RU', $offer->getParams()[0]->getAlias());
        $this->assertFalse($offer->getParams()[1]->isRequired());
        $this->assertTrue($offer->getParams()[2]->isRequired());
        $this->assertEquals([], $offer->getParams()[3]->getParserOptions());
    }
}
