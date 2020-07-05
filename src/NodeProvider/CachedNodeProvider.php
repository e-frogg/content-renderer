<?php


namespace Efrogg\ContentRenderer\NodeProvider;


use Efrogg\ContentRenderer\Core\Resolver\SortableSolverInterface;
use Efrogg\ContentRenderer\Decorator\DecoratorAwareTrait;
use Efrogg\ContentRenderer\Decorator\DecoratorInterface;
use Efrogg\ContentRenderer\Node;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CachedNodeProvider implements NodeProviderInterface
{
    use DecoratorAwareTrait;
    /**
     * @var NodeProviderInterface
     */
    private $nodeProvider;
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * TODO : not implemented
     * @var int
     */
    private $TTL=60;

    public function __construct(NodeProviderInterface $nodeProvider,CacheInterface $cache)
    {
        $this->nodeProvider = $nodeProvider;
        $this->cache = $cache;
    }

    public function getNodeById(string $nodeId): Node
    {
        return $this->cache->get($nodeId,function(ItemInterface $item) {
            $item->expiresAfter($this->getTTL());
            // ici, item->key == nodeId
            return $this->nodeProvider->getNodeById($item->getKey());
        });
    }

    public function addDecorator(DecoratorInterface $decorator): void
    {
        $this->nodeProvider->addDecorator($decorator);
    }

    public function getDecorators(): array
    {
        return $this->nodeProvider->getDecorators();
    }

    public function canResolve($solvable, string $resolverName): bool
    {
        return true;
    }

    /**
     * @return int
     */
    public function getTTL(): int
    {
        return $this->TTL;
    }

    /**
     * @param  int  $TTL
     */
    public function setTTL(int $TTL): void
    {
        $this->TTL = $TTL;
    }
}