<?php

namespace Efrogg\ContentRenderer\DependencyInjection\Compiler;

use Efrogg\ContentRenderer\NodeProvider\CachedNodeProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Contracts\Cache\CacheInterface;

class CachePass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
//        /** @var string[] $cacheServices */
        if (!$container->hasParameter('cms.cache.service')) {
            return;
        }
        $cacheServiceId = $container->getParameter('cms.cache.service');

        if(empty($cacheServiceId)) {
            return;
        }
//            if(!$cacheServiceId instanceof Reference) {
//                throw new \Exception('cache must be a reference to a service');
//            }
        $cacheServiceDefinition = $container->getDefinition($cacheServiceId);


//        $cacheServiceDefinition = $container->getParameter('cms.cache');
//        dd($cacheServiceDefinition)

        // no check because if we inject a Cache directly, Definition is a "ChildDefinition"... so class is empty
        if ($cacheServiceDefinition->getClass() && !is_subclass_of($cacheServiceDefinition->getClass(), CacheInterface::class)) {
            // in case of direct cache service, check is possible
            throw new \Exception(sprintf('cache must be subclass of CacheInterface. %s found', $cacheServiceDefinition->getClass()));
        }


        // cached node provider vient décorer le node provider

        $originalNodeProviderDefinition = $container->getDefinition('cms.node_provider_resolver');

        $definition = $container->register('cms.node_provider', CachedNodeProvider::class)
                  ->addArgument($originalNodeProviderDefinition)
                  ->addArgument($cacheServiceDefinition)
        ;

        if($container->hasParameter('cms.cache.ttl') && $ttl=$container->getParameter('cms.cache.ttl')) {
            $definition
                      ->addMethodCall('setTTL', [$ttl]);
        }

        // add logger service
        if($container->hasParameter('cms.logger.service')) {
            $definition->addMethodCall('setLogger',[$container->getDefinition($container->getParameter('cms.logger.service'))]);
        }
    }
}
