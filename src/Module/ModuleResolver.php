<?php


namespace Efrogg\ContentRenderer\Module;


use Efrogg\ContentRenderer\Core\Resolver\Exception\InvalidSolvableException;
use Efrogg\ContentRenderer\Core\Resolver\Exception\InvalidSolverException;
use Efrogg\ContentRenderer\Core\Resolver\Exception\SolverNotFoundException;
use Efrogg\ContentRenderer\Core\Resolver\Resolver;
use Efrogg\ContentRenderer\Core\Resolver\SolverInterface;
use Efrogg\ContentRenderer\DataProvider\DataProviderAwareTrait;
use Efrogg\ContentRenderer\DataProvider\DataProviderAwareInterface;
use Efrogg\ContentRenderer\DataProvider\DataProviderResolver;
use Efrogg\ContentRenderer\ModuleRenderer\ModuleRendererInterface;
use Efrogg\ContentRenderer\Node;

/**
 * Class ModuleResolver
 * @package Efrogg\ContentRenderer\Module
 *
 * @method ModuleRendererInterface resolve(Node $solvable)
 */
class ModuleResolver extends Resolver
{
    use DataProviderAwareTrait;

    protected $solvableName='node';
    protected $solverName='module';


    public function __construct(DataProviderResolver $dataProviderResolver)
    {
        $this->setDataProviderResolver($dataProviderResolver);
    }

    /**
     * @param  SolverInterface  $solver
     * @throws InvalidSolverException
     */
    public function addSolver(SolverInterface $solver): void
    {
        parent::addSolver($solver);
        if($solver instanceof ModuleInterface) {
            $this->addDataProviderManager($solver);
        }
    }

    private function addDataProviderManager(ModuleInterface $module): void
    {
        if ($module instanceof DataProviderAwareInterface && null !== $this->dataProviderResolver) {
            $module->setDataProviderResolver($this->dataProviderResolver);
        }
    }

    /**
     * @deprecated
     * @param  Node  $node
     * @return ModuleInterface
     * @throws InvalidSolvableException
     * @throws SolverNotFoundException
     */
    public function getModuleForNode(Node $node): ModuleInterface
    {
        return $this->resolve($node);
    }

    /**
     * @param  DataProviderResolver  $dataProviderManager
     * @return ModuleResolver
     */
    public function setDataProviderResolver(DataProviderResolver $dataProviderManager): ModuleResolver
    {
        $this->dataProviderResolver = $dataProviderManager;

        // on ajoute le provider aux modules déjà là
        foreach ($this->solvers as $module) {
            if($module instanceof ModuleInterface) {
                $this->addDataProviderManager($module);
            }
        }
        return $this;
    }

    protected function isValidSolver(SolverInterface $solver): bool
    {
        return $solver instanceof ModuleInterface;
    }

    protected function isValidSolvable($solvable): bool
    {
        return $solvable instanceof Node;
    }

}