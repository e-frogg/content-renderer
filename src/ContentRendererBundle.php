<?php


namespace Efrogg\ContentRenderer;

use Efrogg\ContentRenderer\DependencyInjection\Compiler\ModulePass;
use Efrogg\ContentRenderer\DependencyInjection\Compiler\ModuleRendererPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContentRendererBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ModuleRendererPass());
        $container->addCompilerPass(new ModulePass());
    }
}