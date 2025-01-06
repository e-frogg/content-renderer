<?php

declare(strict_types=1);

namespace Efrogg\ContentRenderer\Event;

class UnpublishEvent extends NodeProviderEvent
{
    public const string NAME = NodeProviderEvents::UNPUBLISH;

}
