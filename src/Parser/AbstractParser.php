<?php

namespace Whitebox\EcommerceImport\Parser;

use Whitebox\EcommerceImport\Parser;

abstract class AbstractParser implements Parser
{
    /**
     * @var string
     */
    protected $errors = [];

    /**
     * @var string
     */
    protected $warnings = [];

    /**
     * @param string $error
     * @return void
     */
    public function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param string $error
     * @return void
     */
    public function addWarning($warning)
    {
        $this->warnings[] = $warning;
    }

    /**
     * @return string[]
     */
    public function getWarnings()
    {
        return $this->warnings;
    }
}
