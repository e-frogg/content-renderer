<?php

namespace Efrogg\ContentRenderer;

interface CmsRendererInterface
{
    public function renderNodeById(string $nodeId, string $subNode = null): string;

}
