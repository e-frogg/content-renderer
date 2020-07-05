<?php


namespace Test\Resolver;


use Efrogg\ContentRenderer\Core\Resolver\Resolver;
use Efrogg\ContentRenderer\Core\Resolver\SolverInterface;

class TestNumberResolver extends Resolver
{

    protected $resolverName='testResolver';
    protected $solverName='number solver';
    protected $solvableName='number';

    protected function isValidSolver(SolverInterface $solver): bool
    {
        return $solver instanceof TestSolverInterface;
    }

    protected function isValidSolvable($solvable): bool
    {
        return is_numeric($solvable) || is_string($solvable);
    }
}