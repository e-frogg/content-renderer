<?php


namespace Efrogg\ContentRenderer;


use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Interface ParameterizableInterface
 * @package Efrogg\ContentRenderer
 *
 * @uses ParameterizableTrait
 */
interface ParameterizableInterface
{
    public function addParameter($key, $value): void;

    public function addParameters(array $keys): void;

    public function getParameters(): ?ParameterBag;

    public function setParameters(?ParameterBag $parameters): void;

}