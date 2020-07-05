<?php


namespace Efrogg\ContentRenderer\ModuleRenderer;


use Efrogg\ContentRenderer\Core\Resolver\Resolver;
use Efrogg\ContentRenderer\Core\Resolver\SolverInterface;
use Efrogg\ContentRenderer\Module\ModuleInterface;

/**
 * Class ModuleRendererResolver
 * @package Efrogg\ContentRenderer\ModuleRenderer
 *
 * @method ModuleRendererInterface resolve(ModuleInterface $solvable)
 * @method void addSolver(ModuleRendererInterface $solver)
 */
class ModuleRendererResolver extends Resolver
{

    protected $solvableName = 'module';
    protected $solverName = 'module renderer';

    protected function isValidSolver(SolverInterface $solver): bool
    {
        return $solver instanceof ModuleRendererInterface;
    }

    protected function isValidSolvable($solvable): bool
    {
        return $solvable instanceof ModuleInterface;
    }

}