<?php


namespace Efrogg\ContentRenderer\DependencyInjection\Compiler;


use Efrogg\ContentRenderer\Core\Resolver\ContainerTag;
use Efrogg\ContentRenderer\Module\ModuleResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ModulePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has(ModuleResolver::class)) {
            return;
        }

        $definition = $container->findDefinition(ModuleResolver::class);

        // find all service IDs with the app.mail_transport tag
        $taggedServices = $container->findTaggedServiceIds(ContainerTag::TAG_MODULE);

        foreach ($taggedServices as $id => $tags) {
            // add the transport service to the TransportChain service
            $definition->addMethodCall('addSolver', [new Reference($id)]);
//            dump("add ",$id);
        }
    }
}