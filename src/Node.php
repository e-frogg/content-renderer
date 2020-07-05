<?php


namespace Efrogg\ContentRenderer;



use Efrogg\ContentRenderer\Core\MagicObject;

/**
 * Node is solvable for ModuleResolver
 *
 * Class Node
 * @package Efrogg\ContentRenderer
 */
class Node extends MagicObject
{
    /**
     * @var array
     */
    private $context;

    public function __construct(array $data=[],array $context=[])
    {
        parent::__construct($data);
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->_type;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @param  array  $context
     */
    public function setContext(array $context): void
    {
        $this->context = $context;
    }

}