<?php

namespace Efrogg\ContentRenderer\Connector\Squidex;


use Efrogg\ContentRenderer\Connector\ConnectorInterface;
use Efrogg\ContentRenderer\Connector\Squidex\Asset\SquidexAsset;
use Efrogg\ContentRenderer\Exception\NodeNotFoundException;
use Efrogg\ContentRenderer\Log\LoggerProxy;
use GuzzleHttp\Exception\BadResponseException;
use Psr\Log\LoggerInterface;

class SquidexConnector implements ConnectorInterface
{
    use LoggerProxy;
    /**
     * @var SquidexClient
     */
    private $client;

    private $appName;

    public function __construct(SquidexClient $client,string $appName,?LoggerInterface $logger=null)
    {
        $this->client = $client;
        $this->appName = $appName;
        $this->initLogger($logger);
    }


    /**
     * @param  array  $ids
     * @return SquidexNode[]
     * @throws BadResponseException
     */
    public function getNodes(array $ids):array
    {
        if(empty($ids)) {
            return  [];
        }

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
     * @param  array  $ids
     * @return SquidexAsset[]
     * @throws BadResponseException
     */
    public function getAssets(array $ids):array
    {
        if(empty($ids)) {
            return  [];
        }

        $result = $this->client->get(
            '/apps/'.$this->appName.'/assets',
            [
                'ids' => implode(',', $ids)
            ]
        );

        $assets = [];
        foreach ($result['items'] as $item) {
            $asset = new SquidexAsset($item['id'],$item['version']);
            $asset->setData($item);
            $assets[$item['id']] = $asset;
        }
        return $assets;
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

        $this->warning('Node not found : '.$nodeId,[
            'title' => 'SquidexConnector'
        ]);
        throw new NodeNotFoundException('node '.$nodeId.' was not found');
    }

    public function setAcceptUnpublished(bool $accept = true): void
    {
        $this->client->setAcceptUnpublished($accept);
    }
}