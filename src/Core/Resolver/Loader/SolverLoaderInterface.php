<?php

namespace Efrogg\ContentRenderer\Core\Resolver\Loader;

interface SolverLoaderInterface
{
    /**
     * @return SolverLoaderInterface[]
     */
    public function getSolvers():array;
}