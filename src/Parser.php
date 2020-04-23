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
}
