<?php


namespace Efrogg\ContentRenderer\Core\Resolver;


interface SolverInterface
{
    /**
     * @param mixed  $solvable
     */
    public function canResolve($solvable, string $resolverName):bool;
}
