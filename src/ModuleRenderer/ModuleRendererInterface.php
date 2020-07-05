<?php


namespace Efrogg\ContentRenderer\ModuleRenderer;


use Efrogg\ContentRenderer\Core\Resolver\SolverInterface;
use Efrogg\ContentRenderer\Core\Resolver\SortableSolverInterface;
use Efrogg\ContentRenderer\Module\ModuleInterface;
use Efrogg\ContentRenderer\Node;
use Efrogg\ContentRenderer\ParameterizableInterface;

interface ModuleRendererInterface extends SortableSolverInterface,SolverInterface, ParameterizableInterface
{
    public function render(ModuleInterface $module,Node $node):string;
}