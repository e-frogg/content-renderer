<?php


namespace Efrogg\ContentRenderer\Decorator;


use Efrogg\ContentRenderer\Node;

class Utf8EncodeNodeDecorator implements DecoratorInterface
{

    /**
     * @param Node $data
     * @return Node
     */
    public function decorate($data)
    {
        return $data->setData(array_map('utf8_encode',$data->getData()));
    }
}