<?php

namespace Efrogg\ContentRenderer\NodeProvider;

interface CachedNodeProviderInterface
{
    /**
     * activates cache
     *
     * @param bool $cacheActive
     *
     * @return void
     */
    public function setCacheActive(bool $cacheActive): void;


    /**
     * enables cache clear on asked node
     *
     * @param bool $cacheReset
     *
     * @return void
     */
    public function setCacheReset(bool $cacheReset): void;

    public function clearCacheById(string $nodeId): bool;


}
