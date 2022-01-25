<?php


namespace Efrogg\ContentRenderer\NodeProvider;


use Efrogg\ContentRenderer\Decorator\DecoratorAwareTrait;
use Efrogg\ContentRenderer\Decorator\DecoratorInterface;
use Efrogg\ContentRenderer\Log\LoggerProxy;
use Efrogg\ContentRenderer\Node;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CachedNodeProvider implements NodeProviderInterface, CachedNodeProviderInterface
{
    use DecoratorAwareTrait, LoggerProxy;

    /**
     * @var NodeProviderInterface
     */
    private $nodeProvider;
    /**
     * @var CacheInterface
     */
    private $cache;
    private $cacheKeyPrefix = 'cms.node.';

    /**
     * TODO : not implemented
     *
     * @var int
     */
    private $TTL = 60;
    private bool $cacheActive = true;
    private bool $cacheReset = false;

    public function __construct(NodeProviderInterface $nodeProvider, CacheInterface $cache)
    {
        $this->nodeProvider = $nodeProvider;
        $this->cache = $cache;
    }

    public function getNodeById(string $nodeId): Node
    {
        $cacheKey = $this->getCacheKey($nodeId);
        if ($this->cacheReset && $this->cache instanceof CacheItemPoolInterface) {
            $this->cache->delete($cacheKey);
        }

        if ($this->cacheActive) {
            //TODO : comprendre pourquoi ce log ne remonte pas dans le logger storyblok??
            $this->info('cache getNodeById : ' . $nodeId);
            return $this->cache->get($cacheKey, function (ItemInterface $item) use ($nodeId) {
                $this->info('generate cache for ' . $this->getTTL());
                $item->expiresAfter($this->getTTL());
                // ici, item->key == nodeId
                return $this->nodeProvider->getNodeById($nodeId);
            });
        }

        $this->info('NO cache getNodeById : ' . $nodeId);
        return $this->nodeProvider->getNodeById($nodeId);
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

    private function encodeKey(string $nodeId): string
    {
        return base64_encode($nodeId);
    }

    public function decodeKey(string $getKey): string
    {
        return base64_decode($getKey);
    }

    public function setCacheActive(bool $cacheActive): void
    {
        $this->cacheActive = $cacheActive;
    }

    public function setCacheReset(bool $cacheReset): void
    {
        $this->cacheReset = $cacheReset;
    }

    /**
     * @param string $nodeId
     *
     * @return string
     */
    public function getCacheKey(string $nodeId): string
    {
        return $this->encodeKey($this->cacheKeyPrefix . $nodeId);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function clearCacheById(string $nodeId): bool
    {
        return $this->cache->delete($this->getCacheKey($nodeId));
    }
}
