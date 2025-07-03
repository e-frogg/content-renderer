<?php

namespace Efrogg\ContentRenderer;

use Efrogg\ContentRenderer\Cache\ControlableCacheInterface;

interface CmsRendererInterface extends ControlableCacheInterface
{
    public function renderNodeById(string $nodeId, ?string $subNode = null): string;
    public function render(Node $node): string;

    /**
     * @param array<string,mixed> $data
     */
    public function convertAndRender($data): ?string;

    /**
     * @param ?array<string,mixed> $data
     */
    public function convertAndRenderMultiple($data): ?string;
}
