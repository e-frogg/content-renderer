<?php


namespace Efrogg\ContentRenderer\Connector\Squidex\Asset;


use Efrogg\ContentRenderer\Asset\Asset;

/**
 * Class SquidexAsset
 * @package Efrogg\ContentRenderer\Connector\Squidex\Asset
 *
 * @property string $_assetId
 * @property string $_version
 */
class SquidexAsset extends Asset
{
    /**
     * @var string
     */
    private $assetId;
    /**
     * @var string|null
     */
    private $version;

    /**
     * SquidexAsset constructor.
     * @param  string       $assetId
     * @param  string|null  $version
     */
    public function __construct(string $assetId, string $version = null)
    {
        parent::__construct();
        $this->assetId = $assetId;
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getAssetId(): string
    {
        return $this->assetId;
    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

//    public function export()
//    {
//        return $this->assetId;
//    }
}