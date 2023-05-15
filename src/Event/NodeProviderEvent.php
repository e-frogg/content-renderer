<?php

namespace Efrogg\ContentRenderer\Event;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\Event;

class NodeProviderEvent extends Event
{
    protected string $nodeId;
    protected ?LoggerInterface $logger;

    public function __construct(string $nodeId, ?LoggerInterface $logger = null)
    {
        $this->nodeId = $nodeId;
        $this->logger = $logger;
    }

    /**
     * @return string
     */
    public function getNodeId(): string
    {
        return $this->nodeId;
    }

    /**
     * @return ?LoggerInterface
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }
}
