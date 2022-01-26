<?php

namespace Efrogg\ContentRenderer;

use Efrogg\ContentRenderer\Cache\ControlableCacheInterface;

interface CmsRendererInterface extends ControlableCacheInterface
{
    public function renderNodeById(string $nodeId, string $subNode = null): string;
}
