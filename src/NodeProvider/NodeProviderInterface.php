<?php


namespace Efrogg\ContentRenderer\NodeProvider;


use Efrogg\ContentRenderer\Cache\CacheAwareInterface;
use Efrogg\ContentRenderer\Core\Resolver\SolverInterface;
use Efrogg\ContentRenderer\Decorator\DecoratorAwareInterface;
use Efrogg\ContentRenderer\Exception\NodeNotFoundException;
use Efrogg\ContentRenderer\Node;

interface NodeProviderInterface extends DecoratorAwareInterface, SolverInterface, CacheAwareInterface, CacheableNodeProviderInterface
{
    /**
     * cacheable fetch
     * @throws NodeNotFoundException
     * @param  string  $nodeId
     * @return Node
     */
    public function getNodeById(string $nodeId):Node;

    /**
     * @param string $nodeId
     *
     * @return Node
     * @internal
     */
    public function fetchNodeById(string $nodeId):Node;

    public function canResolve($solvable, string $resolverName):bool;

}
