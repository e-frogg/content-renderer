<?php

namespace Efrogg\ContentRenderer\EventListener;

use Efrogg\ContentRenderer\Event\NodeProviderEvents;
use Efrogg\ContentRenderer\Event\PublishEvent;
use Efrogg\ContentRenderer\Event\UnpublishEvent;
use Efrogg\ContentRenderer\NodeProvider\CacheableNodeProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CacheClearEventSubscriber implements EventSubscriberInterface
{

    /** @var iterable<CacheableNodeProviderInterface> */
    protected iterable $clearableOnPublish;
    /** @var iterable<CacheableNodeProviderInterface> */
    protected iterable $clearableOnUnpublish;

    public function __construct(
        iterable $clearableOnPublish,
        iterable $clearableOnUnPublish
    )
    {
        $this->clearableOnPublish = $clearableOnPublish;
        $this->clearableOnUnpublish = $clearableOnUnPublish;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            NodeProviderEvents::PUBLISH => 'onPublish',
            NodeProviderEvents::UNPUBLISH => 'onUnpublished',
        ];
    }

    public function onPublish(PublishEvent $event): void
    {
        foreach ($this->clearableOnPublish as $nodeProvider) {
            $nodeProvider->clearCacheById($event->getNodeId());
        }
    }

    public function onUnpublished(UnpublishEvent $event): void
    {
        foreach ($this->clearableOnUnpublish as $nodeProvider) {
            $nodeProvider->clearCacheById($event->getNodeId());
        }
    }

}
