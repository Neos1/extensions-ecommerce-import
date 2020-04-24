<?php

namespace Whitebox\EcommerceImport;

interface Parser
{
    /**
     * @param string $file
     * @param Schema $schema
     * @return Entity[]
     */
    public function parse($file, Schema $schema);

    /**
     * @return string[]
     */
    public function getErrors();

    /**
     * @return string[]
     */
    public function getWarnings();
}
