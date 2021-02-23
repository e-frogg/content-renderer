<?php


namespace Efrogg\ContentRenderer\Asset;


use Efrogg\ContentRenderer\Cache\ControlableCacheInterface;

interface AssetDataManagerInterface extends ControlableCacheInterface
{
    public function deleteAsset($id): bool;

    public function setAsset($id, array $data): bool;

    public function getAsset($id):?array;
}