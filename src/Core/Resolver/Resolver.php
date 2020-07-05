<?php


namespace Efrogg\ContentRenderer\Core\Resolver;


use Efrogg\ContentRenderer\Core\Resolver\Exception\InvalidSolvableException;
use Efrogg\ContentRenderer\Core\Resolver\Exception\InvalidSolverException;
use Efrogg\ContentRenderer\Core\Resolver\Exception\SolverNotFoundException;
use Efrogg\ContentRenderer\Core\Resolver\Loader\SolverLoaderInterface;

abstract class Resolver
{
    /**
     * @var SolverInterface[]
     */
    protected $solvers = [];
    /**
     * @var bool
     */
    private $needSortSolvers = false;

    /**
     * @var string
     */
    protected $solverName='solver';
    /**
     * @var string
     */
    protected $solvableName='solvable';

    /**
     * the name of the solver. Useful in case of resolver for multiple resolvable
     * @var string
     */
    protected $resolverName='';

    /**
     * the class of the exception thrown when resolution fails
     * @var string
     */
    protected $notFoundExceptionClass=SolverNotFoundException::class;


    /**
     * @param  SolverInterface  $solver
     * @throws InvalidSolverException
     */
    public function addSolver(SolverInterface $solver): void
    {
        if (!$this->isValidSolver($solver)) {
            throw new InvalidSolverException(get_class($solver).' is not a valid '.$this->getSolverName());
        }
        $this->solvers[] = $solver;
        $this->needSortSolvers = $solver instanceof SortableSolverInterface;
    }

    /**
     * @param  SolverLoaderInterface  $solverLoader
     * @throws InvalidSolverException
     */
    public function addSolverLoader(SolverLoaderInterface $solverLoader): void
    {
        $this->addSolvers($solverLoader->getSolvers());
    }

    /**
     * @param  array  $solvers
     * @throws InvalidSolverException
     */
    public function addSolvers(array $solvers): void
    {
        foreach ($solvers as $solver) {
            $this->addSolver($solver);
        }
    }

    /**
     * @param  mixed  $solvable
     * @param  int    $max
     * @return SolverInterface[]
     * @throws InvalidSolvableException
     */
    public function resolveAll($solvable, $max = 0): array
    {
        $resolved = [];

        if (!$this->isValidSolvable($solvable)) {
            $solvableType =
                is_string($solvable) ? $solvable :
                is_array($solvable) ? 'array' :
                get_class($solvable);
//            dd($solvable,get_class($this));
            throw new InvalidSolvableException($solvableType.' is not a valid '.$this->getSolvableName());
        }
        if ($this->needSortSolvers) {
            $this->sortSolvers();
        }

        foreach ($this->solvers as $solver) {
            if ($solver->canResolve($solvable, $this->resolverName)) {
                $resolved[] = $solver;

                // test if limit is reached
                if ($max > 0 && count($resolved) >= $max) {
                    break;
                }
            }
        }

        return $resolved;
    }

    /**
     * @param  mixed  $solvable
     * @return SolverInterface
     * @throws InvalidSolvableException
     * @throws SolverNotFoundException
     */
    public function resolve($solvable): SolverInterface
    {
        $solvers = $this->resolveAll($solvable, 1);
        if (!empty($solvers)) {
            return reset($solvers);
        }

        $this->throwSolverNorFoundException($solvable);
    }

    /**
     * sorts solvers by priority DESC
     */
    private function sortSolvers(): void
    {
        usort(
            $this->solvers,
            static function ($solverA, $solverB) {
                return
                    ($solverB instanceof SortableSolverInterface ? $solverB->getPriority() : 0)
                    <=>
                    ($solverA instanceof SortableSolverInterface ? $solverA->getPriority() : 0);
            }
        );
        $this->needSortSolvers = false;
    }

    /**
     * @return string
     */
    public function getSolvableName(): string
    {
        return $this->solvableName;
    }

    /**
     * @return string
     */
    public function getSolverName(): string
    {
        return $this->solverName;
    }

    public function foreachSolvers(callable $callBack)
    {
        foreach ($this->solvers as $solver) {
            $callBack($solver);
        }
    }


    abstract protected function isValidSolver(SolverInterface $solver): bool;

    abstract protected function isValidSolvable($solvable): bool;

    /**
     * @param $solvable
     * @throws SolverNotFoundException
     */
    protected function throwSolverNorFoundException($solvable): void
    {
        $solvableName = '??';
        if (is_object($solvable)) {
            $solvableName = get_class($solvable);
        } elseif (is_string($solvable) || is_numeric($solvable)) {
            $solvableName = $solvable;
        }

        $exceptionClass = $this->notFoundExceptionClass;
        /** @var SolverNotFoundException $exception */
        $exception = new $exceptionClass('solvable ['.$solvableName.'] has no valid solver for '.get_class($this));
        throw $exception;
    }

}