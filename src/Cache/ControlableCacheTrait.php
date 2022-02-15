<?php


namespace Efrogg\ContentRenderer\Cache;


trait ControlableCacheTrait
{

    /** @var bool  */
    protected $useCache = true;

    /** @var bool  */
    protected $updateCache = false;

    /**
     * @var array<bool>
     */
    protected $updateCacheStack=[];

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
        if ($this instanceof CacheAwareInterface) {
            $cache = $this->getCache();
            if ($cache instanceof ControlableCacheInterface) {
                $cache->setUseCache($useCache);
            }
        }
    }

    /**
     * @return bool
     */
    public function isUpdateCache(): bool
    {
        return $this->updateCache;
    }

    /**
     * @param bool $updateCache
     * @param bool $isTemporaryChange
     */
    public function setUpdateCache(bool $updateCache, bool $isTemporaryChange = false): void
    {
        if($isTemporaryChange) {
            $this->updateCacheStack[]=$this->updateCache;
        }
        $this->updateCache = $updateCache;
    }

    public function restoreUpdateCache(): void
    {
        if(empty($this->updateCacheStack)) {
            throw new \LogicException('restoreUpdateCache : stack is empty. You must not call restoreUpdateCache more than setUpdateCache');
        }
        $this->updateCache = array_pop($this->updateCacheStack);

    }

}
