<?php


namespace Efrogg\ContentRenderer\Core\Resolver\Loader;


use Efrogg\ContentRenderer\Module\ModuleInterface;

class NamespaceSolverLoader implements SolverLoaderInterface
{
    /**
     * @var String
     */
    private $path;
    /**
     * @var String
     */
    private $namespace;

    /**
     * ModulePathLoader constructor.
     * @param  String  $path
     * @param  string  $namespace
     */
    public function __construct(string $path,string $namespace)
    {
        $this->path = realpath($path);
        $this->namespace = $namespace;
    }

    /**
     * @return ModuleInterface[]
     */
    public function getSolvers(): array
    {
        $solvers = [];
        foreach (glob($this->path.'/*.php') as $file) {
            $info = pathinfo($file);
            $className = $this->namespace.'\\'.$info['filename'];
            $solvers[] = $this->moduleFactory($className);
        }
        return $solvers;
    }

    protected function moduleFactory(string $className)
    {
        return new $className();
    }
}