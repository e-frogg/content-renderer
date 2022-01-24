<?php

namespace Efrogg\ContentRenderer;

class TwigPathCollector
{

    private array $paths = [];

    public function register(string $path, int $priority = 1): void
    {
        $this->paths[$priority][] = $path;
    }

    /**
     * @return array<string>
     */
    public function getSortedPaths(): array
    {
        ksort($this->paths);
        return array_merge(...$this->paths);
    }
}
