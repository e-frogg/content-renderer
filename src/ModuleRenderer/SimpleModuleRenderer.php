<?php


namespace Efrogg\ContentRenderer\ModuleRenderer;


use Efrogg\ContentRenderer\Module\ModuleInterface;
use Efrogg\ContentRenderer\Module\RenderableModuleInterface;
use Efrogg\ContentRenderer\Node;
use Efrogg\ContentRenderer\ParameterizableTrait;

/**
 * this renderer delegates rendering directly to the module.
 * Modules implementing RenderableModuleInterface will render directly through this renderer
 *
 * Class SimpleModuleRenderer
 * @package Efrogg\ContentRenderer\ModuleRenderer
 */
class SimpleModuleRenderer implements ModuleRendererInterface
{
    use ParameterizableTrait;

    public function render(ModuleInterface $module, Node $node): string
    {
        if($module instanceof RenderableModuleInterface) {
            return $module->render($node);
        }
    }

    public function canResolve($solvable, string $resolverName): bool
    {
        return $solvable instanceof RenderableModuleInterface;
    }

    public function getPriority(): int
    {
        return 0;
    }
}