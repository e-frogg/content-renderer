<?php

namespace Efrogg\ContentRenderer\NodeProvider;

use Efrogg\ContentRenderer\Cache\CacheKeyEncoderInterface;
use Efrogg\ContentRenderer\Cache\ControlableCacheInterface;

interface CacheableNodeProviderInterface extends ControlableCacheInterface, CacheKeyEncoderInterface
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
     * @return int
     */
    public function getTTL(): int;

    /**
     * @param int $TTL
     */
    public function setTTL(int $TTL): void;

}
