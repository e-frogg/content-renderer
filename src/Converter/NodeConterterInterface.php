<?php


namespace Efrogg\ContentRenderer\Converter;


use Efrogg\ContentRenderer\Node;

interface NodeConterterInterface
{
    public function convert(Node $node);

}