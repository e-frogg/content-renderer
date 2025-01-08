<?php

declare(strict_types=1);

namespace Efrogg\ContentRenderer\DependencyInjection;

class ContentRendererConfig
{
    /**
     * @param array{
     *     twig: array{
     *          namespace: string,
     *          extension: string,
     *          debug_mode: bool,
     *          path: array{
     *              separator: string,
     *              max_depth: int
     *          }
     *     },
     *     cache: array{
     *          ttl: int,
     *          storage: array{
     *              php_path: string,
     *              json_path: string
     *          }
     *     },
     *     logger: array{
     *          default: string
     *    },
     *     services: array{
     *          cache_handler: string
     *   }
     * } $config
     */
    public function __construct(
        private array $config,
    ) {
    }

    public function getTwigNamespace(): string
    {
        return $this->config['twig']['namespace'];
    }

    public function getTwigExtension(): string
    {
        return $this->config['twig']['extension'];
    }

    public function getTwigDebugMode(): bool
    {
        return $this->config['twig']['debug_mode'];
    }

    public function getTwigPathSeparator(): string
    {
        return $this->config['twig']['path']['separator'];
    }

    public function getTwigPathMaxDepth(): int
    {
        return $this->config['twig']['path']['max_depth'];
    }

    public function getCacheTtl(): int
    {
        return $this->config['cache']['ttl'];
    }

    public function getCachePhpPath(): string
    {
        return $this->config['cache']['storage']['php_path'];
    }

    public function getCacheJsonPath(): string
    {
        return $this->config['cache']['storage']['json_path'];
    }

    public function getLoggerDefault(): string
    {
        return $this->config['logger']['default'];
    }

    public function getServicesCacheHandler(): string
    {
        return $this->config['services']['cache_handler'];
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
