<?php

declare(strict_types=1);

namespace Efrogg\ContentRenderer\Event;

class PublishEvent extends NodeProviderEvent
{
    public const string NAME = NodeProviderEvents::PUBLISH;

}
