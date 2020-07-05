<?php


namespace Efrogg\ContentRenderer\Cache;


use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\Cache\CacheInterface;

abstract class AbstractContentCache implements CacheInterface, CacheItemPoolInterface
{
    protected $debugMode = false;

    protected $useCache = true;

    protected $updateCache = false;

    /**
     * @return bool
     */
    public function isUseCache(): bool
    {
        return $this->useCache;
    }

    /**
     * @param  bool  $useCache
     */
    public function setUseCache(bool $useCache): void
    {
        $this->useCache = $useCache;
    }

    /**
     * @return bool
     */
    public function isDebugMode(): bool
    {
        return $this->debugMode;
    }

    /**
     * @param  bool  $debugMode
     */
    public function setDebugMode(bool $debugMode): void
    {
        $this->debugMode = $debugMode;
    }

    public function debug(...$logs): void
    {
        if($this->isDebugMode()) {
            dump(...$logs);
        }
    }

    /**
     * @return bool
     */
    public function isUpdateCache(): bool
    {
        return $this->updateCache;
    }

    /**
     * @param  bool  $updateCache
     */
    public function setUpdateCache(bool $updateCache): void
    {
        $this->updateCache = $updateCache;
    }

    public function contentGetCacheItem($key,callable $feedItemCallback) {
        $item = new Item($key);
        if($this->hasItem($key)) {
            if($this->isUpdateCache()) {
                $this->delete($key);
            } else {
                $item->setIsHit(true);
                $feedItemCallback($key,$item);
            }
        }
        return $item;
    }
}