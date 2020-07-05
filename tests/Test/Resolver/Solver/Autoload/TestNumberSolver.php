<?php


namespace Test\Resolver\Solver\Autoload;


use Efrogg\ContentRenderer\Core\Resolver\SolverInterface;
use Efrogg\ContentRenderer\Core\Resolver\SortableSolverInterface;
use Test\Resolver\Solver\AbstractTestSolver;

class TestNumberSolver extends AbstractTestSolver implements SolverInterface, SortableSolverInterface
{

    private $min;
    private $max;
    /**
     * @var int
     */
    private $priority;

    /**
     * TestNumberSolver constructor.
     * @param  int     $min
     * @param  int     $max
     * @param  int     $priority
     * @param  string  $name
     */
    public function __construct(int $min = 0, int $max = 10, int $priority = 0, string $name = '0-10')
    {
        $this->setName($name);
        $this->min = $min;
        $this->max = $max;
        $this->priority = $priority;
    }


    public function canResolve($solvable, string $resolverName): bool
    {
        return
            is_numeric($solvable)
            //            && $resolverName === 'testResolver'
            && $solvable >= $this->min
            && $solvable <= $this->max;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}