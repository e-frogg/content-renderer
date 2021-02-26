<?php


namespace Efrogg\ContentRenderer\Event;


use Efrogg\ContentRenderer\Node;
use Symfony\Component\EventDispatcher\Event;

class NodeEvent extends Event
{

    public const UPDATED = 'NodeEvent::UPDATED';
    public const CREATED = 'NodeEvent::CREATED';
    public const DELETED = 'NodeEvent::DELETED';
    /**
     * @var ?mixed
     */
    protected $nodeId;

    /** @var ?Node */
    private $node;

    /**
     * CacheNodeEvent constructor.
     * @param ?Node $node
     * @param null $nodeId
     */
    public function __construct(?Node $node, $nodeId = null)
    {
        $this->node = $node;
        if (null === $nodeId && null !== $node) {
            $this->nodeId = $node->getNodeId();
        } else {
            $this->nodeId = $nodeId;
        }
    }

    /**
     * @return Node
     */
    public function getNode(): Node
    {
        return $this->node;
    }

    /**
     * @return ?string
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }


}