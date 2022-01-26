<?php

namespace Efrogg\ContentRenderer\Log;

class LogEntry
{
    /**
     * @var mixed
     */
    protected $level;
    /**
     * @var string|\Stringable
     */
    protected $message;
    /**
     * @var array
     */
    protected $context;

    /**
     * @param mixed              $level
     * @param string|\Stringable $message
     * @param array              $context
     */
    public function __construct($level, $message, array $context)
    {
        $this->level = $level;
        $this->message = $message;
        $this->context = $context;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return string|\Stringable
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
