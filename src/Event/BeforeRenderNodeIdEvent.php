<?php

declare(strict_types=1);

namespace Efrogg\ContentRenderer\Event;

readonly class BeforeRenderNodeIdEvent
{
    public function __construct(
        private string $nodeId,
    ) {
    }

    /**
     * @return string
     */
    public function getNodeId(): string
    {
        return $this->nodeId;
    }
}
