<?php


namespace Efrogg\ContentRenderer\Decorator;


class UTF8DecodeStringDecorator implements DecoratorInterface
{

    public function decorate($data)
    {
        if (is_string($data)) {
            // return utf8_decode($data);
            // Problème de conversion de certains caractères (Ex:?)
            return iconv("UTF-8", "CP1252", $data);
        }
        return $data;
    }
}
