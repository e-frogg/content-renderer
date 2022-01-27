<?php

namespace Efrogg\ContentRenderer\NodeProvider;

use Efrogg\ContentRenderer\Cache\CacheAwareTrait;
use Efrogg\ContentRenderer\Cache\ControlableCacheTrait;
use Efrogg\ContentRenderer\Cache\JsonDumperCache;
use Efrogg\ContentRenderer\Exception\NodeNotFoundException;
use Efrogg\ContentRenderer\Log\LoggerProxy;
use Efrogg\ContentRenderer\Node;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;

trait CacheableNodeProviderTrait
{
    use LoggerProxy;
    use CacheAwareTrait;
    use ControlableCacheTrait;


    protected int $TTL = 60;


    /**
     * @throws InvalidArgumentException
     */
    public function clearCacheById(string $nodeId): bool
    {
        if($this->hasCache()) {
            return $this->cache->delete($this->getCacheKey($nodeId));
        }
        return true;
    }

    /**
     * @param string $nodeId
     *
     * @return string
     */
    public function getCacheKey(string $nodeId): string
    {
        return $this->encodeKey($this->getCacheKeyPrefix() . $nodeId);
    }

    public function encodeKey(string $nodeIdWithPrefix): string
    {
        if($this->hasCache() && $this->cache instanceof JsonDumperCache) {
            return $nodeIdWithPrefix;
        }

        return base64_encode($nodeIdWithPrefix);
    }

    /**
     * @return int
     */
    public function getTTL(): int
    {
        return $this->TTL;
    }

    /**
     * @param int $TTL
     */
    public function setTTL(int $TTL): void
    {
        $this->TTL = $TTL;
    }

    /**
     * @param string $nodeId
     *
     * @return Node
     * @throws InvalidArgumentException
     * @throws NodeNotFoundException
     * @throws Exception
     */
    public function getNodeById(string $nodeId): Node
    {
//        var_dump("coucou",get_class($this));
        $shortName = (new \ReflectionClass($this))->getShortName();

        $this->info('getNodeById : ' . $nodeId .'('.$shortName.')');
        if ($this->hasCache()) {
            $cacheShortName = (new \ReflectionClass($this->cache))->getShortName();
            $this->debug('cache : '.$cacheShortName.')');
            $cacheKey = $this->getCacheKey($nodeId);

                dump("aaaaaaaaaaaaaaaaaaaaaaaa fsfd wsdfs dfgdfsg dsfg sdfg sdfjghdsjfhg ksjlfhg lkjqfdh glkjshdfiugh elruighiueqjrfng lkjdshf goiuqlekjrng leqiulhfk");
            if ($this->isUpdateCache() && $this->cache instanceof CacheItemPoolInterface) {
                $this->warning('delete : ' . $cacheKey);
                $this->cache->delete($cacheKey);
            }

            if ($this->isUseCache()) {
                $this->debug('get cache : ' . $cacheKey);
                //TODO : comprendre pourquoi ce log ne remonte pas dans le logger storyblok??
                return $this->cache->get($cacheKey, function (ItemInterface $item) use ($nodeId) {
                    $this->info('generate for ' . $this->getTTL());
                    $item->expiresAfter($this->getTTL());
                    // ici, item->key == nodeId
                    return $this->fetchNodeById($nodeId);
                });
            }
        }

        $this->debug('NO cache getNodeById : ' . $nodeId." : ".$shortName);
        return $this->fetchNodeById($nodeId);
    }

}
