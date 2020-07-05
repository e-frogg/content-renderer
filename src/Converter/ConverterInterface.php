<?php


namespace Efrogg\ContentRenderer\Converter;


use Efrogg\ContentRenderer\Asset\Asset;
use Efrogg\ContentRenderer\Decorator\DecoratorAwareInterface;
use Efrogg\ContentRenderer\Node;

interface ConverterInterface extends DecoratorAwareInterface
{
    /**
     * @param $sourceData
     * @return Node|Asset
     */
    public function convert($sourceData);
}