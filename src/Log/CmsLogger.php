<?php
namespace Efrogg\ContentRenderer\Log;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
class CmsLogger implements LoggerInterface
{
    use LoggerTrait;

    /**
     * @var array
     */
    private $logs=[];

    /**
     * @param array<mixed> $context
     */
    public function log($level, $message, array $context = array()): void
    {
        $this->logs[]=new LogEntry($level,$message,$context);
    }

    /**
     * @return array
     */
    public function getLogs(): array
    {
        return $this->logs;
    }
}
