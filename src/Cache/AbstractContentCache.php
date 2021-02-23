<?php


namespace Efrogg\ContentRenderer\Cache;


use Efrogg\ContentRenderer\Log\LoggerProxy;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\Cache\CacheInterface;

abstract class AbstractContentCache implements CacheInterface, CacheItemPoolInterface, ControlableCacheInterface
{
    use LoggerProxy;
    use ControlableCacheTrait;

    protected $debugMode = false;


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