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
        if(!$container->hasParameter('storyblok.cache')) {
            return;
        }
        $cacheServiceId = $container->getParameter('storyblok.cache');

        if(empty($cacheServiceId)) {
            return;
        }
//            if(!$cacheServiceId instanceof Reference) {
//                throw new \Exception('cache must be a reference to a service');
//            }
        try {
            $cacheServiceDefinition = $container->getDefinition($cacheServiceId);
        } catch (ServiceNotFoundException $e) {
            throw $e;
        }

        // no check because if we inject a Cache directly, Definition is a "ChildDefinition"... so class is empty
        if ($cacheServiceDefinition->getClass() && !is_subclass_of($cacheServiceDefinition->getClass(), CacheInterface::class)) {
            // in case of direct cache service, check is possible
            throw new \Exception(sprintf('cache must be subclass of CacheInterface. %s found', $cacheServiceDefinition->getClass()));
        }


        // TODO : ajouter du cache en amont du NodeProvider actuel ...

        // cached node provider vient décorer le node provider

        $originalNodeProviderDefinition = $container->getDefinition('cms.node_provider_resolver');

        $container->register('cms.node_provider', CachedNodeProvider::class)
                  ->addArgument($originalNodeProviderDefinition)
                  ->addArgument($cacheServiceDefinition)//                 ->addMethodCall('setTTL', [$this->environment->get('cms.node_cache_ttl')])
        ;
//        dd($param);
        // TODO: Implement process() method.
    }
}
