<?php


namespace Efrogg\ContentRenderer\Module;


use Efrogg\ContentRenderer\Node;

interface RenderableModuleInterface
{
    public function render(Node $node):string;
}