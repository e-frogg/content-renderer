<?php

namespace Efrogg\ContentRenderer\Connector\Squidex;


use Efrogg\ContentRenderer\Connector\ConnectorInterface;
use Efrogg\ContentRenderer\Exception\NodeNotFoundException;
use GuzzleHttp\Exception\BadResponseException;

class SquidexConnector implements ConnectorInterface
{
    /**
     * @var SquidexClient
     */
    private $client;

    private $appName;

    public function __construct(SquidexClient $client,string $appName)
    {
        $this->client = $client;
        $this->appName = $appName;
    }


    /**
     * @param  array  $ids
     * @return SquidexNode[]
     * @throws BadResponseException
     */
    public function getNodes(array $ids):array
    {
            $result = $this->client->get(
                '/content/'.$this->appName.'/',
                [
                    'ids' => implode(',', $ids)
                ]
            );

        $nodes = [];
        foreach ($result['items'] as $item) {
            $node = new SquidexNode($item);
            $nodes[$node->getId()] = $node;
        }
        return $nodes;
    }

    /**
     * @param $nodeId
     * @return SquidexNode
     * @throws BadResponseException
     * @throws NodeNotFoundException
     */
    public function getNodeById($nodeId):SquidexNode
    {
        $nodes = $this->getNodes([$nodeId]);
        if(isset($nodes[$nodeId])) {
            return $nodes[$nodeId];
        }

        throw new NodeNotFoundException('node '.$nodeId.' was not found');
    }

    public function setAcceptUnpublished(bool $accept = true): void
    {
        $this->client->setAcceptUnpublished($accept);
    }
}