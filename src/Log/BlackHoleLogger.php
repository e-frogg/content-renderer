<?php


namespace Efrogg\ContentRenderer\Log;


use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class BlackHoleLogger implements LoggerInterface
{
    use LoggerTrait;

    /**
     * @param array<mixed> $context
     */
    public function log($level, $message, array $context = array()): void
    {
    }
}
