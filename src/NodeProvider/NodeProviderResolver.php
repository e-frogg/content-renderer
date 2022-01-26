<?php


namespace Efrogg\ContentRenderer\NodeProvider;


use Efrogg\ContentRenderer\Cache\CacheAwareTrait;
use Efrogg\ContentRenderer\Core\Resolver\Exception\InvalidSolvableException;
use Efrogg\ContentRenderer\Core\Resolver\Exception\InvalidSolverException;
use Efrogg\ContentRenderer\Core\Resolver\Resolver;
use Efrogg\ContentRenderer\Core\Resolver\SolverInterface;
use Efrogg\ContentRenderer\Decorator\DecoratorAwareTrait;
use Efrogg\ContentRenderer\Decorator\DecoratorInterface;
use Efrogg\ContentRenderer\Exception\NodeNotFoundException;
use Efrogg\ContentRenderer\Node;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class NodeProviderResolver extends Resolver implements NodeProviderInterface,LoggerAwareInterface
{
    use DecoratorAwareTrait {
        DecoratorAwareTrait::addDecorator as addDecoratorFromTrait;
    }
    use CacheAwareTrait;
    use CacheableNodeProviderTrait;
    use LoggerAwareTrait;

    protected $solverName = 'node provider';
    protected $solvableName = 'node id';

    protected function isValidSolver(SolverInterface $solver): bool
    {
        return $solver instanceof NodeProviderInterface;
    }

    protected function isValidSolvable($solvable): bool
    {
        return is_string($solvable);
    }

    /**
     * @param  SolverInterface|NodeProviderInterface  $solver
     * @throws InvalidSolverException
     */
    public function addSolver(SolverInterface $solver): void
    {
        parent::addSolver($solver);

        // on ajoute les décorateurs au node provider
        foreach ($this->getDecorators() as $decorator) {
            $solver->addDecorator($decorator);
        }
    }

    /**
     * @param  DecoratorInterface  $decorator
     */
    public function addDecorator(DecoratorInterface $decorator): void
    {
        // on le stocke ici, pour l'ajouter plus tard a chaque node provider qui viendrait plus tard
        $this->addDecoratorFromTrait($decorator);

        // on ajoute le décorateur a tous les providers
        $this->foreachSolvers(function(NodeProviderInterface $nodeProvider) use ($decorator) {
            $nodeProvider->addDecorator($decorator);
        });
    }

    /**
     * @param  string  $nodeId
     * @return Node
     * @throws NodeNotFoundException
     * @throws InvalidSolvableException
     */
    public function fetchNodeById(string $nodeId): Node
    {
        $nodeProviders = $this->resolveAll($nodeId);

        foreach ($nodeProviders as $nodeProvider) {
            try {
                return $nodeProvider->getNodeById($nodeId);
            } catch(NodeNotFoundException $exception) {
                continue;
            }
        }

        throw new NodeNotFoundException('no NodeProvider found for node '.$nodeId);
    }

    public function canResolve($solvable, string $resolverName): bool
    {
        return true;
    }

    public function getCacheKeyPrefix(): string
    {
        return 'cms.resolver.';
    }
}
