<?php

namespace Whitebox\EcommerceImport\Parser;

use RuntimeException;
use Whitebox\EcommerceImport\Parser;

class Factory
{
    /**
     * @param string $name
     * @return Parser
     */
    public static function create($name)
    {
        switch ($name) {
            case 'yml':
                return new YMLParser();
                break;
            default:
                throw new RuntimeException('Unsupported parser: ' . $name);
        }
    }
}
