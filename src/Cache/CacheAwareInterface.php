<?php

namespace Efrogg\ContentRenderer\Cache;

use Symfony\Contracts\Cache\CacheInterface;

interface CacheAwareInterface
{
    public function setCache(CacheInterface $cache): void;
    public function getCache(): CacheInterface;
    public function hasCache(): bool;
}
