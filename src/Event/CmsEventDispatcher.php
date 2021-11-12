<?php


namespace Efrogg\ContentRenderer\Event;


use Efrogg\ContentRenderer\Log\LoggerProxy;
use Efrogg\ContentRenderer\Node;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * sert a faire communiquer les différents éléments (notamment le cache)
 * Class CmsEventDispatcher
 */
class CmsEventDispatcher extends EventDispatcher
{
    use LoggerProxy;

    public function dispatchNodeEventFromNodeId(string $eventName, $nodeId): Event
    {
        return $this->dispatchWithTypedReturn(new NodeEvent(null, $nodeId), $eventName);
    }

    public function dispatchAssetEventFromAssetId(string $eventName, $assetId): Event
    {
        return $this->dispatchWithTypedReturn(new AssetEvent(null,$assetId),$eventName);
    }

    /** @deprecated  pas encore testé */
    private function dispatchNodeEventFromNode(string $eventName, Node $node):Event
    {
        return $this->dispatchWithTypedReturn(new NodeEvent($node),$eventName);
    }

    public function dispatch(object $event, string $eventName = null): object
    {
        // TODO : selon version symfony, inverser les paramètres
        $this->debug(sprintf('dispatch %s', $eventName));
        return parent::dispatch($event,$eventName);
    }

    private function dispatchWithTypedReturn(object $event, string $eventName = null): NodeEvent
    {
        $returnEvent = $this->dispatch($event, $eventName);
        assert($returnEvent instanceof NodeEvent);
        return $returnEvent;
    }
}
