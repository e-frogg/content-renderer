<?php


namespace Efrogg\ContentRenderer\Core\Resolver;


interface SolverInterface
{
    public function canResolve($solvable, string $resolverName):bool;
}