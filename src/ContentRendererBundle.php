<?php

namespace Efrogg\ContentRenderer;

use Efrogg\ContentRenderer\DependencyInjection\ContentRendererExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContentRendererBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        // Ici, vous pourrez ajouter des CompilerPass si nécessaire
        // $container->addCompilerPass(new CachePass());
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new ContentRendererExtension();
    }
}
