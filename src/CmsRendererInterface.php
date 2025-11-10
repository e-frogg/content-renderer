<?php

declare(strict_types=1);

namespace Efrogg\ContentRenderer;

use Efrogg\ContentRenderer\Cache\ControlableCacheInterface;

interface CmsRendererInterface extends ControlableCacheInterface
{
    public function renderNodeById(string $nodeId, ?string $subNode = null): string;

    public function render(Node $node): string;

    /**
     * @param array<string,mixed>  $data
     * @param ?array<string,mixed> $additionalData
     */
    public function convertAndRender($data, ?array $additionalData = []): ?string;

    /**
     * @param mixed|array<string,mixed> $data
     * @param ?array<string,mixed>      $additionalData
     */
    public function convertAndRenderMultiple($data, ?array $additionalData = []): ?string;
}
