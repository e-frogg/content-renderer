<?php


namespace Test\Resolver\Solver\Autoload;


use Efrogg\ContentRenderer\Core\Resolver\SolverInterface;
use Test\Resolver\Solver\AbstractTestSolver;

class TestStringSolver extends AbstractTestSolver implements SolverInterface
{


    /**
     * TestNumberSolver constructor.
     * @param  string  $name
     */
    public function __construct(string $name = 'string solver')
    {
        $this->setName($name);
    }


    public function canResolve($solvable, string $resolverName): bool
    {
        return !is_numeric($solvable);
    }

}