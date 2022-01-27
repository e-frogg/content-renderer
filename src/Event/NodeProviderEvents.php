<?php

namespace Efrogg\ContentRenderer\Event;

class NodeProviderEvents
{
    /**
     * The RESPONSE event occurs once a response was created for
     * replying to a request.
     *
     * This event allows you to modify or replace the response that will be
     * replied.
     *
     * @Event("Efrogg\ContentRenderer\Event\PublishEvent")
     */
    public const PUBLISH = 'cms.node_provider.publish';
    /**
     * @Event("Efrogg\ContentRenderer\Event\UnpublishEvent")
     */
    public const UNPUBLISH = 'cms.node_provider.unpublish';
}
