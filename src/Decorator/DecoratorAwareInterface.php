<?php


namespace Efrogg\ContentRenderer\Decorator;


interface DecoratorAwareInterface
{
    public function addDecorator(DecoratorInterface $decorator):void;

    /**
     * @return DecoratorInterface[]
     */
    public function getDecorators():array;
}