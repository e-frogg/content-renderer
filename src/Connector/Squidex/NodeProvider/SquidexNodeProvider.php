<?php


namespace Efrogg\ContentRenderer\Connector\Squidex\NodeProvider;


use Efrogg\ContentRenderer\Asset\AssetDataManagerInterface;
use Efrogg\ContentRenderer\Connector\ConnectorInterface;
use Efrogg\ContentRenderer\Connector\Squidex\Asset\AbstractSquidexAssetDataManager;
use Efrogg\ContentRenderer\Connector\Squidex\Asset\SquidexAsset;
use Efrogg\ContentRenderer\Connector\Squidex\SquidexConnector;
use Efrogg\ContentRenderer\Connector\Squidex\SquidexTools;
use Efrogg\ContentRenderer\Converter\Keyword;
use Efrogg\ContentRenderer\Decorator\DecoratorAwareTrait;
use Efrogg\ContentRenderer\Event\NodeRelationCacheManagerInterface;
use Efrogg\ContentRenderer\Exception\NodeNotFoundException;
use Efrogg\ContentRenderer\Log\LoggerProxy;
use Efrogg\ContentRenderer\Node;
use Efrogg\ContentRenderer\NodeProvider\NodeProviderInterface;
use Exception;
use GuzzleHttp\Exception\BadResponseException;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Ubaldi\Cms\Cache\NodeRelationCacheManager;

class SquidexNodeProvider implements NodeProviderInterface
{
    use DecoratorAwareTrait;
    use LoggerProxy;

    /**
     * @var ConnectorInterface
     */
    private $connector;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var AssetDataManagerInterface
     */
    private $assetManager;

    /**
     * @var int
     */
    private $TTL=1800;

    /**
     * @var NodeRelationCacheManagerInterface
     */
    private $nodeRelationCacheManager;


    public function __construct(SquidexConnector $connector,?LoggerInterface $logger=null)
    {
        $this->connector = $connector;
        $this->initLogger($logger);
    }

    public function canResolve($solvable, string $resolverName): bool
    {
        return SquidexTools::isNodeId($solvable);
    }

    public function setAcceptUnpublished(bool $accept=true):void
    {
        $this->connector->setAcceptUnpublished($accept);
    }

    /**
     * @param  string  $nodeId
     * @return Node
     * @throws NodeNotFoundException
     * @throws BadResponseException
     * @throws InvalidArgumentException
     */
    public function getNodeById(string $nodeId): Node
    {
//        $startTime = microtime(true);
        if(null !== $this->cache) {
            $rawNodeData = $this->cache->get($nodeId,function(ItemInterface $item) {
                $item->expiresAfter($this->getTTL());
//                $item->expiresAfter(new \DateInterval('P1D'));
//                $item->expiresAt(new \DateTime('11:00'));
                return $this->getRawNodeData($item->getKey());
            });
        } else {
            $rawNodeData = $this->getRawNodeData($nodeId);
        }

        if(isset($this->nodeRelationCacheManager)) {
            $this->nodeRelationCacheManager->setActive(true);
        }
        $nodeData = $this->convertData($rawNodeData['data']);
//        $this->info('getNodeById : '.$nodeId,['duration'=>microtime(true)-$startTime,'title'=>'SquidexNodeProvider']);
        return new Node($nodeData, $rawNodeData['context']);
    }

    /**
     * @param  string  $nodeId
     * @return array
     * @throws NodeNotFoundException
     * @throws BadResponseException
     */
    private function getRawNodeData(string $nodeId): array
    {
        $node = $this->connector->getNodeById($nodeId);
        return [
            'data'    => array_merge(
                [Keyword::NODE_TYPE => $node->getSchemaName()],
                $node->getData()
            ),
            'context' => $node->getContext()
        ];
    }

    private function convertData(array $data):array
    {
        $converted = [];
        foreach ($data as $k => $datum) {
            try {
                $converted[$k] = $this->convertOneField($datum);
            } catch (Exception $exception) {
                dd('no data for node "'.$k.'"',$exception);
                $converted[$k] = null;
                // node not found ?
            }
        }
//        dump($converted);
        return $converted;
    }

    private function convertOneField($datum, $tryNode = true)
    {
        if (is_array($datum) && isset($datum['iv'])) {
            $iv = $datum['iv'];
        } else {
            $iv = $datum;
        }

        if (is_string($iv)) {
            return $this->decorate($iv);
        }

        if (is_array($iv)) {
            $children = [];
            foreach ($iv as $key => $oneIv) {
                if (is_string($oneIv) && SquidexTools::isNodeId($oneIv)) {
                    // commence par les assets, car les nodes ont un fallback vers l'api => call inutile si c'est une asset
                    if ($asset = $this->getAsset($oneIv)) {
                        // on a trouvé un asset
                        $children[$key] = $asset;
                    } else {
                        // on cherche un node
                        try {
                            // on a bien le node
                            $children[$key] = $this->getNodeById($oneIv);
                        } catch (NodeNotFoundException $exception) {
                            // ce n'est pas une asset référencée, ni un node
                            // on retourne un asset sans infos, juste le nom
                            // en espérant que ça marche ...
                            if ($asset = $this->findAsset($oneIv)) {
                                $children[$key] = $asset;
                            } else {
                                $this->error('Node or Asset not found : '.$oneIv,[
                                    'title' => 'SquidexConnector'
                                ]);

                                // pas de node, pas d'asset... '
//                                throw new NodeNotFoundException();
                            }
                        }
                    }
                } else {
                    $children[$key] = $this->convertOneField($oneIv, false);
//                        dd("iv inconnu",$oneIv);
                }
            }
            return $children;
        }

        return $iv;
    }

    private function getAsset(string $oneIv): ?SquidexAsset
    {
        if (null !== $this->assetManager) {
            if ($assetData = $this->assetManager->getAsset($oneIv)) {
                return new SquidexAsset($oneIv, $assetData['version']);
            }
            // non trouvée, on fait quoi ?
            // return null => ce ne sera pas une asset (image vide)
            // si on passe => asset sans version (et si ce n'est pas une asset ?)
            return null;
        }

        // on le cherche via l'API
        return $this->findAsset($oneIv);
    }

    /**
     * cherche un asset par son id dans l'api
     * retourne null si l'asset n'a pas été trouvé
     * @param string $oneIv
     * @return SquidexAsset|null
     * @throws BadResponseException
     */
    private function findAsset(string $oneIv): ?SquidexAsset
    {
//        $startTime = microtime(true);
        $assets = $this->connector->getAssets([$oneIv]);

        if (null !== $this->assetManager) {
            foreach ($assets as $asset) {
                $this->assetManager->setAsset(
                    $oneIv,
                    AbstractSquidexAssetDataManager::getDataForCache($asset)
                );
            }
        }
//        $this->info('findAsset : '.$oneIv,['duration'=>microtime(true)-$startTime,'title'=>'SquidexNodeProvider']);
        return $assets[$oneIv] ?? null;
    }


    /**
     * @return CacheInterface
     */
    public function getCache(): CacheInterface
    {
        return $this->cache;
    }

    /**
     * @param  CacheInterface  $cache
     */
    public function setCache(CacheInterface $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * @return int
     */
    public function getTTL(): int
    {
        return $this->TTL;
    }

    /**
     * @param  int  $TTL
     */
    public function setTTL(int $TTL): void
    {
        $this->TTL = $TTL;
    }

    /**
     * @return AssetDataManagerInterface
     */
    public function getAssetManager(): AssetDataManagerInterface
    {
        return $this->assetManager;
    }

    /**
     * @param  AssetDataManagerInterface  $assetManager
     */
    public function setAssetManager(AssetDataManagerInterface $assetManager): void
    {
        $this->assetManager = $assetManager;
    }

    /**
     * @param NodeRelationCacheManager $nodeRelationCacheManager
     */
    public function setNodeRelationCacheManager(NodeRelationCacheManager $nodeRelationCacheManager): void
    {
        $this->nodeRelationCacheManager = $nodeRelationCacheManager;
    }

}