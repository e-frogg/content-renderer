<?php

namespace Efrogg\ContentRenderer\Core\Resolver\Loader;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class ContainerTagSolverLoader implements SolverLoaderInterface
{
    /**
     * @var ContainerBuilder
     */
    private $container;
    /**
     * @var string
     */
    private $tagName;

    /**
     * ContainerTagSolverLoader constructor.
     * @param  ContainerBuilder  $container
     * @param  string            $tagName
     */
    public function __construct(ContainerBuilder $container, string $tagName)
    {
        $this->container = $container;
        $this->tagName = $tagName;
    }


    public function getSolvers(): array
    {
        $solvers = [];
        foreach ($this->container->findTaggedServiceIds($this->tagName) as $serviceName => $serviceConfig) {
            $solvers[] = $this->container->get($serviceName);
        }

        return $solvers;
    }
}
