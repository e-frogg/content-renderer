<?php

declare(strict_types=1);

namespace Efrogg\ContentRenderer\Event;

class FilterNodeDataEvent
{
    protected bool $hidden = false;

    /**
     * @param array<string,mixed> $content
     */
    public function __construct(
        private array $content
    ) {
    }

    /**
     * @return array<string,mixed>
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * @param array<string,mixed> $content
     */
    public function setContent(array $content): void
    {
        $this->content = $content;
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
