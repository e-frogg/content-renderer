<?php

namespace Efrogg\ContentRenderer\NodeProvider;

use Efrogg\ContentRenderer\Cache\ControlableCacheInterface;

interface CacheableNodeProviderInterface extends ControlableCacheInterface
{

    /**
     * @param string $nodeId
     *
     * @return bool
     */
    public function clearCacheById(string $nodeId): bool;

    /**
     * @param string $nodeId
     *
     * @return string
     */
    public function getCacheKey(string $nodeId): string;

    /**
     * @return string
     */
    public function getCacheKeyPrefix(): string;

    /**
     * @param string $nodeId
     *
     * @return string
     */
    public function encodeKey(string $nodeId): string;

    /**
     * @return int
     */
    public function getTTL(): int;

    /**
     * @param int $TTL
     */
    public function setTTL(int $TTL): void;

}
