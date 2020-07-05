<?php


namespace Efrogg\ContentRenderer\Module;


use Efrogg\ContentRenderer\Node;

interface DataModuleInterface
{
    public function getNodeData(Node $node):array;
}