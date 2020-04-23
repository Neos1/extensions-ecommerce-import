<?php

namespace Whitebox\EcommerceImport\Schema;

class Entity
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Param[]
     */
    protected $params = [];

    /**
     * @param string $name
     * @param Param[] $keys
     */
    public function __construct($name, array $params)
    {
        $this->name = $name;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Param[]
     */
    public function getParams()
    {
        return $this->params;
    }
}
