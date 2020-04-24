<?php

namespace Whitebox\EcommerceImport\Test\Parser;

use PHPUnit\Framework\TestCase;
use Sirian\YMLParser\Param;
use Whitebox\EcommerceImport\Schema;

/**
 * @covers Whitebox\EcommerceImport\Parser\YMLParser
 */
class YMLParserTest extends TestCase
{
    public function testParse()
    {
        $schema = Schema::createFromYaml(file_get_contents(__DIR__ . '/schema.yaml'));
        $parser = $schema->getParser();
        $entities = $parser->parse(__DIR__ . '/import.yml', $schema);
        $this->assertEmpty(
            $parser->getErrors(),
            'There should be no errors, but we got: ' . implode(', ', $parser->getErrors())
        );
        $this->assertEmpty(
            $parser->getWarnings(),
            'There should be no warnings, but we got some: ' . implode(', ', $parser->getWarnings())
        );
        $this->assertTrue(is_array($entities));
        $this->assertCount(3, $entities);
        $this->assertCount(3, array_filter($entities, function ($entity) {
            return $entity->getName() === 'offer';
        }), 'There should be 3 offers');
        $entity1 = $entities[0];
        $this->assertEquals('Набор для спагетти 20пр Studio', $entity1->name);
        $this->assertEquals(16470, $entity1->price);
        $this->assertEquals('RUR', $entity1->currency);
        $this->assertEquals('BergHOFF', $entity1->brand);
        $this->assertEquals('1100890', $entity1->ean);
        $this->assertNotEmpty($entity1->description);
        $this->assertCount(9, $entity1->pictures);
        $this->assertCount(12, $entity1->params);
        /** @var Param[] */
        $entity1_params = $entity1->params;
        $this->assertInstanceOf(Param::class, $entity1_params['цвет']);
        $this->assertEquals('Металл', $entity1_params['цвет']->getValue());
        $this->assertInstanceOf(Param::class, $entity1_params['материал']);
        $this->assertEquals(
            "18/10 нержавеющая сталь фарфор",
            preg_replace("/\s{2,}/", ' ', trim($entity1_params['материал']->getValue()))
        );
        $this->assertInstanceOf(Param::class, $entity1_params['вес']);
        $this->assertEquals(11.14, $entity1_params['вес']->getValue());
        $this->assertEquals('кг', $entity1_params['вес']->getUnit());
    }
}
