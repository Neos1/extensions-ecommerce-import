<?php

namespace Whitebox\EcommerceImport;

use RuntimeException;
use Symfony\Component\Yaml\Yaml;
use Whitebox\EcommerceImport\Parser\Factory as ParserFactory;
use Whitebox\EcommerceImport\Schema\Entity;
use Whitebox\EcommerceImport\Schema\Param;

class Schema
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var Entity[]
     */
    protected $entities;

    /**
     * @param Parser $parser
     * @param Entity[] $entities
     */
    public function __construct(Parser $parser, array $entities)
    {
        $this->parser = $parser;
        $this->entities = $entities;
    }

    /**
     * @return Entity[]
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @param string $name
     * @return Entity|null
     */
    public function getEntity($name)
    {
        $filtered = array_filter($this->entities, function (Entity $entity) use ($name) {
            return $entity->getName() === $name;
        });
        if (count($filtered) > 0) {
            return reset($filtered);
        }
        return null;
    }

    /**
     * @return Parser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * @param string $yaml
     * @return self
     */
    public static function createFromYaml($yaml)
    {
        return static::createFromArray(Yaml::parse($yaml));
    }

    /**
     * @param array $schema
     * @return self
     */
    public static function createFromArray(array $schema)
    {
        if (!isset($schema['parser']) || !is_string($schema['parser'])) {
            throw new RuntimeException('Schema - expected "parser" property to be a string');
        }
        if (!isset($schema['entities']) || !is_array($schema['entities'])) {
            throw new RuntimeException('Schema - expected "entities" property to be an array');
        }
        $entities = [];
        foreach ($schema['entities'] as $name => $values) {
            if (is_array($values)) {
                $params = [];
                foreach ($values as $key => $data) {
                    $param = new Param($key, isset($data['alias']) ? $data['alias'] : null);
                    if (isset($data['required'])) {
                        $param->setRequired($data['required']);
                    }
                    if (isset($data['parser_options'])) {
                        $param->setParserOptions($data['parser_options']);
                    }
                    if (isset($data['type'])) {
                        $param->setType($data['type']);
                    }
                    if (isset($data['default'])) {
                        $param->setDefault($data['default']);
                    }
                    if (isset($params[$param->getName()])) {
                        throw new RuntimeException('Duplicate parameter: ' . $param->getName());
                    }
                    $params[$param->getName()] = $param;
                }
                $entities[] = new Entity($name, $params);
            }
        }
        return new static(ParserFactory::create($schema['parser']), $entities);
    }
}
