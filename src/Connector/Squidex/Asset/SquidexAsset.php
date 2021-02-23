<?php


namespace Efrogg\ContentRenderer\Connector\Squidex\Asset;


use Efrogg\ContentRenderer\Asset\Asset;

/**
 * Class SquidexAsset
 * @package Efrogg\ContentRenderer\Connector\Squidex\Asset
 *
 * @property string $assetId
 * @property string $version
 *
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
     * @var ?string
     */
    private  $assetType;

    /**
     * @var ?bool
     */
    private $isImage;

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

    /**
     * @return string|null
     */
    public function getAssetType(): ?string
    {
        return $this->assetType;
    }

    /**
     * @return bool|null
     */
    public function getIsImage(): ?bool
    {
        return $this->isImage;
    }



//    public function export()
//    {
//        return $this->assetId;
//    }
}