<?php

namespace Efrogg\ContentRenderer\Cache;

use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\CacheInterface;

class DummyCache implements CacheInterface
{

    protected static $createCacheItem;

    public function __construct()
    {
        self::$createCacheItem ?? self::$createCacheItem = \Closure::bind(
            static function ($key) {
                $item = new CacheItem();
                $item->key = $key;
                $item->isHit = false;

                return $item;
            },
            null,
            CacheItem::class
        );
    }

    public function get(string $key, callable $callback, float $beta = null, array &$metadata = null)
    {
        $item = $this->getItem($key);

        $save = false;
        return $callback($item, $save);
    }

    public function delete(string $key): bool
    {
        return true;
    }

    protected function getItem(string $key)
    {
        $value = null;
        return (self::$createCacheItem)($key, $value, false);
    }

}


