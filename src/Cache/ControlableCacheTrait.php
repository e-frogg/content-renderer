<?php


namespace Efrogg\ContentRenderer\Cache;


trait ControlableCacheTrait
{

    protected $useCache = true;

    protected $updateCache = false;

    /**
     * @return bool
     */
    public function isUseCache(): bool
    {
        return $this->useCache;
    }

    /**
     * @param  bool  $useCache
     */
    public function setUseCache(bool $useCache): void
    {
        $this->useCache = $useCache;
    }

    /**
     * @return bool
     */
    public function isUpdateCache(): bool
    {
        return $this->updateCache;
    }

    /**
     * @param  bool  $updateCache
     */
    public function setUpdateCache(bool $updateCache): void
    {
        $this->updateCache = $updateCache;
    }
}