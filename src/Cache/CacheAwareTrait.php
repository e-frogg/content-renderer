<?php

namespace Efrogg\ContentRenderer\Cache;

use Symfony\Contracts\Cache\CacheInterface;

trait CacheAwareTrait
{

    protected CacheInterface $cache;

    public function setCache(CacheInterface $cache): void
    {
        $this->cache = $cache;
    }

    public function getCache(): CacheInterface
    {
        return $this->cache;
    }

    public function hasCache(): bool
    {
        return isset($this->cache);
    }

}
