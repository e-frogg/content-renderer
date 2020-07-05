<?php


namespace Efrogg\ContentRenderer\Decorator;


class UTF8DecodeStringDecorator implements DecoratorInterface
{

    public function decorate($data)
    {
        if (is_string($data)) {
            return utf8_decode($data);
        }
        return $data;
    }
}