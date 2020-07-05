<?php


namespace Test\Resolver;


interface TestSolverInterface
{
    public function getName(): string;
    public function setName(string $name):void;
}