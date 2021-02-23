<?php


namespace Efrogg\ContentRenderer\Cache;


interface ControlableCacheInterface
{

    /**
     * @return bool
     */
    public function isUseCache(): bool;

    /**
     * @param bool $useCache
     */
    public function setUseCache(bool $useCache): void;

    /**
     * @return bool
     */
    public function isUpdateCache(): bool;

    /**
     * @param bool $updateCache
     */
    public function setUpdateCache(bool $updateCache): void;
}