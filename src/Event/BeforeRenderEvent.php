<?php

declare(strict_types=1);

namespace Efrogg\ContentRenderer\Event;

use Efrogg\ContentRenderer\Module\ModuleInterface;
use Efrogg\ContentRenderer\Node;

class BeforeRenderEvent
{
    protected bool $hidden = false;

    public function __construct(
        private ModuleInterface $module,
        private Node $node
    ) {
    }

    public function getModule(): ModuleInterface
    {
        return $this->module;
    }

    public function setModule(ModuleInterface $module): void
    {
        $this->module = $module;
    }

    public function getNode(): Node
    {
        return $this->node;
    }

    public function setNode(Node $node): void
    {
        $this->node = $node;
    }

    public function setHidden(bool $hide = true): void
    {
        $this->hidden = $hide;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }
}
