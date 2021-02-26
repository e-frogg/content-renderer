<?php


namespace Efrogg\ContentRenderer\Event;


use Efrogg\ContentRenderer\Node;
use Symfony\Component\EventDispatcher\Event;

class CacheEvent extends Event
{

    public const CACHE_SAVE = 'CacheEvent::CACHE_SAVE';
    public const CACHE_CLEAR = 'CacheEvent::CACHE_CLEAR';
    protected $id;
    /**
     * @var ?Node
     */
    protected $node;

    /**
     * CacheNodeEvent constructor.
     * @param $id
     * @param ?Node $node
     */
    public function __construct($id,?Node $node=null)
    {
        $this->id = $id;
        $this->node = $node;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ?Node
     */
    public function getNode(): ?Node
    {
        return $this->node;
    }

}