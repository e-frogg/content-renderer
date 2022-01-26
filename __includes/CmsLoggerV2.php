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

    public function log($level, string|\Stringable $message, array $context = [])
    {
        // stores logs for rendering in the profiler bar
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
