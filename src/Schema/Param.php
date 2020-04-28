<?php

namespace Whitebox\EcommerceImport\Schema;

class Param
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type = 'string';

    /**
     * @var mixed
     */
    protected $default = null;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var boolean
     */
    protected $required = false;

    /**
     * @var mixed
     */
    protected $parserOptions;
    
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
     * @param mixed $value
     * @return boolean
     */
    public function isValidValue($value)
    {
        $is_valid = true;
        switch ($this->type) {
            case 'string':
                $is_valid = is_string($value);
                break;
            case 'integer':
            case 'int':
                $is_valid = is_integer($value);
                break;
            case 'boolean':
            case 'bool':
                $is_valid = is_bool($value);
                break;
            case 'float':
                $is_valid = is_float($value);
                break;
            case 'numeric':
                $is_valid = is_numeric($value);
                break;
            case 'array':
                $is_valid = is_array($value);
                break;
        }
        return $is_valid;
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
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $default
     * @return void
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $options
     * @return void
     */
    public function setParserOptions($options)
    {
        $this->parserOptions = $options;
    }

    /**
     * @return mixed
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
