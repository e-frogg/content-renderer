<?php


namespace Efrogg\ContentRenderer\Event;


class AssetEvent extends NodeEvent
{

    public const UPDATED = 'AssetEvent::UPDATED';
    public const CREATED = 'AssetEvent::CREATED';
    public const DELETED = 'AssetEvent::DELETED';

}
