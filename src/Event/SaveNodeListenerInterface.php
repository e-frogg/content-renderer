<?php


namespace Efrogg\ContentRenderer\Event;


interface SaveNodeListenerInterface
{
    public function onCacheSave(CacheEvent $cacheNodeEvent): void;
}