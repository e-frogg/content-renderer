<?php


namespace Efrogg\ContentRenderer\DataProvider;


use Efrogg\ContentRenderer\Core\Resolver\SolverInterface;

interface DataProviderInterface extends SolverInterface
{
    public function getData();

    /**
     * just for type hint
     * @param  string  $solvable
     * @param  string  $resolverName
     * @return bool
     */
    public function canResolve($solvable, string $resolverName): bool;

}