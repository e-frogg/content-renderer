<?php


namespace Efrogg\ContentRenderer\Asset;


interface AssetDataManagerInterface
{

    public function deleteAsset($id): bool;

    public function setAsset($id, array $data): bool;

    public function getAsset($id):?array;
}