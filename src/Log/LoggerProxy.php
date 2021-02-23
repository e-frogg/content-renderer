<?php


namespace Efrogg\ContentRenderer\Log;


use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

trait LoggerProxy
{
    use LoggerAwareTrait;
    use LoggerTrait;

    public function log($level, $message, array $context = array())
    {
        if (null !== $this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }

    public function initLogger(?LoggerInterface $logger)
    {
        if(null !== $logger) {
            $this->setLogger($logger);
        }
    }

}