<?php


namespace Efrogg\ContentRenderer\Log;


use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class BlackHoleLogger implements LoggerInterface
{
    use LoggerTrait;

    public function log($level, $message, array $context = array())
    {
    }
}