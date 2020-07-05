<?php


namespace Efrogg\ContentRenderer\Core\Resolver;


interface SortableSolverInterface
{
    public function getPriority():int;
}