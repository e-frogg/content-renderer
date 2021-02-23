<?php

namespace Efrogg\ContentRenderer\Connector\Squidex\Asset;

use Efrogg\ContentRenderer\Asset\AssetDataManagerInterface;

abstract class AbstractSquidexAssetDataManager implements AssetDataManagerInterface
{

    /**
     * @param array|\ArrayAccess $asset
     * @return array
     */
    public static function getDataForCache($asset)
    {
        if (!is_array($asset) && !$asset instanceof \ArrayAccess) {
            throw new \Exception(
                'AbstractSquidexAssetDataManager : asset data must be an array or implement ArrayAccess'
            );
        }
        return array_filter(
            [
                'mimeType' => $asset['mimeType'] ?? null,
                'version'  => $asset['version'] ?? null,
                'type'     => $asset['assetType'] ?? null,
                'isImage'  => $asset['isImage'] ?? null,
            ]
        );
    }
}
