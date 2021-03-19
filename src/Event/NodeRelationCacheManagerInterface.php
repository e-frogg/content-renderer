<?php


namespace Efrogg\ContentRenderer\Event;


interface NodeRelationCacheManagerInterface
{
    public function onCacheSave(CacheEvent $cacheNodeEvent): void;

    /**
     * @return bool
     */
    public function isActive(): bool;

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void;
}