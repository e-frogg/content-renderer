<?php


namespace Efrogg\ContentRenderer\Log;


use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class DumperLogger implements LoggerInterface
{
    use LoggerTrait;

    /**
     * @param array<mixed> $context
     */
    public function log($level, $message, array $context = array()): void
    {
        dump(sprintf('[%s] : %s',$level,$message));
    }
}
