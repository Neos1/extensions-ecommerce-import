<?php

namespace Whitebox\EcommerceImport\Schema;

use Whitebox\EcommerceImport\Parser\Options;

class Param
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var boolean
     */
    protected $required = false;

    /**
     * @var Options[]
     */
    protected $parserOptions = [];
    
    /**
     * @param string $name
     * @param string|null $alias
     */
    public function __construct($name, $alias = null)
    {
        $this->name = $name;
        $this->alias = $alias ?: $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }
    
    /**
     * @param boolean $required
     * @return void
     */
    public function setRequired($required)
    {
        $this->required = (bool) $required;
    }

    /**
     * @param Options[] $options
     * @return void
     */
    public function setParserOptions(array $options)
    {
        $this->parserOptions = $options;
    }

    /**
     * @return Options[]
     */
    public function getParserOptions()
    {
        return $this->parserOptions;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }
}
