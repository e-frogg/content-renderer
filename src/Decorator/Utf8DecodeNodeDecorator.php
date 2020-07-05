<?php


namespace Efrogg\ContentRenderer\Decorator;


use Efrogg\ContentRenderer\Node;

class Utf8DecodeNodeDecorator implements DecoratorInterface
{

    /**
     * @param Node $data
     * @return Node
     */
    public function decorate($data)
    {
        return $data->setData(array_map('utf8_decode',$data->getData()));
    }
}