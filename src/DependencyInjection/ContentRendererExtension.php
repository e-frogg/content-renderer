<?php

namespace Efrogg\ContentRenderer\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ContentRendererExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        // Configuration des paramètres Twig
        $container->setParameter('cms.twig.namespace', $config['twig']['namespace']);
        $container->setParameter('cms.twig.extension', $config['twig']['extension']);
        $container->setParameter('cms.twig.debug_mode', $config['twig']['debug_mode']);
        $container->setParameter('cms.twig.path_separator', $config['twig']['path']['separator']);
        $container->setParameter('cms.twig.path_max_depth', $config['twig']['path']['max_depth']);

        // Configuration du cache
        $container->setParameter('cms.cache.ttl', $config['cache']['ttl']);
        $container->setParameter('cms.cache.php.storage-path', $config['cache']['storage']['php_path']);
        $container->setParameter('cms.cache.json.storage-path', $config['cache']['storage']['json_path']);

        // Configuration du logger
        $container->setAlias('cms.logger', sprintf('cms.logger.%s', $config['logger']['default']));

        // Configuration du cache handler
        $container->setAlias('cms.cache', sprintf('cms.cache.%s', $config['services']['cache_handler']));

//        dump(array_filter($container->getParameterBag()->all(), function ($key) {
//            return strpos($key, 'cms') === 0;
//        }, ARRAY_FILTER_USE_KEY));
    }

    public function getAlias(): string
    {
        return 'content_renderer';
    }
}
