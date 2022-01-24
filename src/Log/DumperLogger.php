<?php


namespace Efrogg\ContentRenderer\Log;


use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class DumperLogger implements LoggerInterface
{
    use LoggerTrait;

    public function log($level, $message, array $context = array())
    {
        dump(sprintf('[%s] : %s',$level,$message));
    }
}
