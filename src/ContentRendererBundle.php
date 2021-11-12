<?php


namespace Efrogg\ContentRenderer;

use Efrogg\ContentRenderer\DependencyInjection\Compiler\ModulePass;
use Efrogg\ContentRenderer\DependencyInjection\Compiler\ModuleRendererPass;
use Efrogg\ContentRenderer\DependencyInjection\Compiler\NodeProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContentRendererBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ModuleRendererPass());
        $container->addCompilerPass(new ModulePass());

        // automaticaly add NodeProviders to the resolver
        $container->addCompilerPass(new NodeProviderPass());
    }
}
