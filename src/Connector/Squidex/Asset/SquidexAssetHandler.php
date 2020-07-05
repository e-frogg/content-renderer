<?php


namespace Efrogg\ContentRenderer\Connector\Squidex\Asset;


use Efrogg\ContentRenderer\Asset\Asset;
use Efrogg\ContentRenderer\Asset\AssetHandlerInterface;
use Efrogg\ContentRenderer\Asset\AssetResolver;
use Efrogg\ContentRenderer\Connector\Squidex\SquidexTools;
use Efrogg\ContentRenderer\Core\Resolver\SolverInterface;

class SquidexAssetHandler implements AssetHandlerInterface, SolverInterface
{

    private $baseUrl;
    private $appName;

    /**
     * SquidexAssetHandler constructor.
     * @param  string  $baseUrl
     * @param  string  $appName
     */
    public function __construct(string $baseUrl, string $appName)
    {
        $this->baseUrl = $baseUrl;
        $this->appName = $appName;
    }


    public function canResolve($solvable, string $resolverName): bool
    {
        return AssetResolver::RESOLVER_NAME === $resolverName &&
               $solvable instanceof SquidexAsset &&
               SquidexTools::isNodeId($solvable->getAssetId());
    }

    /**
     * @param  SquidexAsset  $asset
     * @param  array         $parameters
     * @return Asset
     */
    public function getAsset($asset, $parameters = []): Asset
    {
        // parameters :
        //version:<long>
        //cache:<long>
        //download:0
        //width:200
        //height:<integer>
        //quality:<integer>
        //mode:<string>
        //      Crop
        //      CropUpsize
        //      Pad
        //      BoxPad
        //      Max
        //      Min
        //focusX:<float>
        //focusY:<float>
        //nofocus:<boolean>
        //force:<boolean>

        // on ajoute un param GET pour assurer le renouvellement CDN
        if(null !== $asset->getVersion()) {
            $parameters['v']=$asset->getVersion();
        }

        $asset->setSrc($this->baseUrl.'/assets/'.$this->appName.'/'.$asset->getAssetId().'/:more?'.http_build_query($parameters));
        return $asset;
    }

    /**
     * @param  string  $baseUrl
     * @return self
     */
    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    /**
     * @param  string  $appName
     * @return self
     */
    public function setAppName(string $appName): self
    {
        $this->appName = $appName;
        return $this;
    }
}