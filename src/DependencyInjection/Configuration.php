<?php

namespace Efrogg\ContentRenderer\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('content_renderer');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('twig')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('namespace')->defaultValue('')->end()
                        ->scalarNode('extension')->defaultValue('.twig')->end()
                        ->booleanNode('debug_mode')->defaultFalse()->end()
                        ->arrayNode('path')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('separator')->defaultValue('-')->end()
                                ->integerNode('max_depth')->defaultValue(0)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('cache')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('ttl')->defaultValue(60)->end()
                        ->arrayNode('storage')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('php_path')->defaultValue('%kernel.cache_dir%/cms/php')->end()
                                ->scalarNode('json_path')->defaultValue('%kernel.cache_dir%/cms/json')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('logger')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->enumNode('default')
                            ->values(['profiler', 'dumper', 'blackhole'])
                            ->defaultValue('profiler')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('services')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->enumNode('cache_handler')
                            ->values(['dummy', 'php', 'json'])
                            ->defaultValue('dummy')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
