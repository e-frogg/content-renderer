<?php


namespace Efrogg\ContentRenderer\Connector\Squidex;


class SquidexTools
{

    public static function isNodeId(string $id):bool
    {
        return preg_match('/^[a-f0-9-]{36}$/i', $id);
    }
}