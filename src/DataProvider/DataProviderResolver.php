<?php


namespace Efrogg\ContentRenderer\DataProvider;


use Efrogg\ContentRenderer\Core\Resolver\Resolver;
use Efrogg\ContentRenderer\Core\Resolver\SolverInterface;

/**
 * Class DataProviderResolver
 * @package Efrogg\ContentRenderer\DataProvider
 *
 * @method DataProviderInterface resolve(string $solvable)
 */
class DataProviderResolver extends Resolver
{

    protected $solverName = 'data provider';
    protected $solvableName = 'data type';
    //TODO : implementer
    protected $notFoundExceptionClass=DataProviderNotFoundException::class;

    protected function isValidSolver(SolverInterface $solver): bool
    {
        return $solver instanceof DataProviderInterface;
    }

    protected function isValidSolvable($solvable): bool
    {
        return is_string($solvable);
    }

    /**
     * @param  DataProviderInterface  $dataProvider
     * @deprecated
     */
    public function addDataProvider(DataProviderInterface $dataProvider): void
    {
        $this->addSolver($dataProvider);
    }

}