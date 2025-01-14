<?php

declare(strict_types=1);

namespace Efrogg\ContentRenderer\Module;

use Efrogg\ContentRenderer\Node;

class SimpleDataModule implements ModuleInterface, DataModuleInterface
{
    /**
     * @param Node $solvable
     */
    public function canResolve($solvable, string $resolverName): bool
    {
        return true;
    }

    public function getPriority(): int
    {
        return 0;
    }

    /**
     * @param Node $node
     *
     * @return array<string, mixed>
     */
    public function getNodeData(Node $node): array
    {
        return $node->getData();
    }
}
