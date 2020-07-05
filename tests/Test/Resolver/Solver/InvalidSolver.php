<?php


namespace Test\Resolver\Solver;


use Efrogg\ContentRenderer\Core\Resolver\SolverInterface;

class InvalidSolver implements SolverInterface
{

    public function canResolve($solvable, string $resolverName): bool
    {
        return true;
    }
}