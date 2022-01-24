<?php


namespace Efrogg\ContentRenderer\NodeProvider;


use Efrogg\ContentRenderer\Decorator\DecoratorAwareTrait;
use Efrogg\ContentRenderer\Decorator\DecoratorInterface;
use Efrogg\ContentRenderer\Log\LoggerProxy;
use Efrogg\ContentRenderer\Node;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CachedNodeProvider implements NodeProviderInterface
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
//        if($this->cache instanceof CacheItemPoolInterface) {
//            dd($this->cache->getItem($this->encodeKey($nodeId)));
//        }
        //TODO : comprendre pourquoi ce log ne remonte pas dans le logger storyblok??
        $this->info('getNodeById : '.$nodeId);
        return $this->cache->get($this->encodeKey($this->cacheKeyPrefix.$nodeId),function(ItemInterface $item) use($nodeId){
            $this->info('generate cache for '.$this->getTTL());
            $item->expiresAfter($this->getTTL());
            // ici, item->key == nodeId
            return $this->nodeProvider->getNodeById($nodeId);
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

    private function encodeKey(string $nodeId): string
    {
        return base64_encode($nodeId);
    }

    public function decodeKey(string $getKey): string
    {
        return base64_decode($getKey);
    }
}
