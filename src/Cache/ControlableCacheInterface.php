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
     * @param bool $isTemporaryChange
     */
    public function setUpdateCache(bool $updateCache, bool $isTemporaryChange = false): void;

    public function restoreUpdateCache(): void;

}
