<?php

namespace Efrogg\ContentRenderer\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * @deprecated
 */
class CacheClearEvent extends Event
{
    public const ACTION_PUBLISH = 'publish';
    public const ACTION_UNPUBLISH = 'unpublish';

    protected string $action;

    public function __construct(string $action)
    {
        $this->action = $action;
    }

    public function isPublishAction(): bool
    {
        return self::ACTION_PUBLISH === $this->action;
    }

    public function isUnpublishAction(): bool
    {
        return self::ACTION_UNPUBLISH === $this->action;
    }
}
