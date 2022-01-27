<?php

namespace Efrogg\ContentRenderer\Event;

use Symfony\Contracts\EventDispatcher\Event;

class NodeProviderEvent extends Event
{
    protected string $nodeId;

    public function __construct(string $nodeId)
    {
        $this->nodeId = $nodeId;
    }

    /**
     * @return string
     */
    public function getNodeId(): string
    {
        return $this->nodeId;
    }
}
