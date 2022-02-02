<?php

declare(strict_types=1);


namespace Efrogg\ContentRenderer\Core\Resolver;


trait SortableSolverTrait
{
    protected int $priority = 0;

    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }
}
