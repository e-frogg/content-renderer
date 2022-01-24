<?php

namespace Efrogg\ContentRenderer\Event;

use Efrogg\ContentRenderer\TwigConfigurator;
use Efrogg\ContentRenderer\TwigPathCollector;
use Symfony\Contracts\EventDispatcher\Event;
use Twig\Environment;

class TwigConfigurationEvent extends Event
{
    protected Environment $environment;
    private TwigPathCollector $pathCollector;
    private array $paths=[];

    public function __construct(Environment $environment, TwigPathCollector $pathCollector)
    {
        $this->environment = $environment;
        $this->pathCollector = $pathCollector;
    }

    /**
     * @return Environment
     */
    public function getEnvironment(): Environment
    {
        return $this->environment;
    }

    public function registerPath(string $path, int $priority = TwigConfigurator::PRIORITY_NORMAL): void
    {
        $this->pathCollector->register($path,$priority);
    }
}
