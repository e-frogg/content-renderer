<?php


namespace Efrogg\ContentRenderer\Event;


use Efrogg\ContentRenderer\Node;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Ubaldi\Cms\Log\QuickLoggerTrait;

/**
 * sert a faire communiquer les diff�rents �l�ments (notamment le cache)
 * Class CmsEventDispatcher
 * @package Ubaldi\Controller\Page\Cms
 */
class CmsEventDispatcher extends EventDispatcher
{
    use QuickLoggerTrait;

    public function dispatchNodeEventFromNodeId(string $eventName, $nodeId): Event
    {
        return $this->dispatch($eventName,new NodeEvent(null,$nodeId));
    }
    public function dispatchAssetEventFromAssetId(string $eventName, $assetId): Event
    {
        return $this->dispatch($eventName,new AssetEvent(null,$assetId));
    }

    /** @deprecated  pas encore test� */
    private function dispatchNodeEventFromNode(string $eventName, Node $node):Event
    {
        return $this->dispatch($eventName,new NodeEvent($node));
    }

    public function dispatch($eventName, Event $event = null)
    {
        $this->quickLog('dispatch',$eventName);
        return parent::dispatch($eventName, $event); // TODO: Change the autogenerated stub
    }
}