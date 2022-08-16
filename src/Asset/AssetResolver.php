<?php


namespace Efrogg\ContentRenderer\Asset;


use Efrogg\ContentRenderer\Core\Resolver\Resolver;
use Efrogg\ContentRenderer\Core\Resolver\SolverInterface;

/**
 * detects the right AssetHandler for a String
 * Class AssetResolver
 * @package Efrogg\ContentRenderer\Asset
 *
 * @method AssetHandlerInterface resolve(mixed $solvable)
 */
class AssetResolver extends Resolver
{
    public const RESOLVER_NAME='asset';

    protected $solvableName='asset';
    protected $solverName='asset handler';
    protected $resolverName=self::RESOLVER_NAME;

    protected function isValidSolver(SolverInterface $solver): bool
    {
        return $solver instanceof AssetHandlerInterface;
    }

    protected function isValidSolvable($solvable): bool
    {
        return $solvable instanceof Asset;
    }
}
