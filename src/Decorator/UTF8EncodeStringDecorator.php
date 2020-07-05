<?php


namespace Efrogg\ContentRenderer\Decorator;


class UTF8EncodeStringDecorator implements DecoratorInterface
{

    public function decorate($data)
    {
        if(is_string($data)) {
            return utf8_encode($data);
        }
        return $data;
    }
}