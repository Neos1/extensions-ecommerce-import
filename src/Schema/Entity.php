<?php

namespace Whitebox\EcommerceImport\Schema;

class Entity
{

    /**
     * @var string
     */
    protected $name;

    /**
     * Indexed by name array of params
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

    /**
     * @param string $name
     * @return boolean
     */
    public function hasParam($name)
    {
        return isset($this->params[$name]);
    }

    /**
     * @param string $name
     * @return Param|null
     */
    public function getParam($name)
    {
        return isset($this->params[$name]) ? $this->params[$name] : null;
    }
}
