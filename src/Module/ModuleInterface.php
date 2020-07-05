<?php


namespace Efrogg\ContentRenderer\Module;


use Efrogg\ContentRenderer\Core\Resolver\SolverInterface;
use Efrogg\ContentRenderer\Core\Resolver\SortableSolverInterface;
use Efrogg\ContentRenderer\Node;

/**
 * module is solvable for ModuleRendererResolver (resolves renderer for module)
 * module is solver for ModuleSolver (resolves module for node)
 * Interface ModuleInterface
 * @package Efrogg\ContentRenderer\Module
 *
 */
interface ModuleInterface extends SolverInterface,SortableSolverInterface
{
    /**
     * just for type hint string
     * @param  Node  $solvable
     * @param  string  $resolverName
     * @return bool
     */
    public function canResolve($solvable, string $resolverName): bool;

}