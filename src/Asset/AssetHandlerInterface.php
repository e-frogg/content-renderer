<?php


namespace Efrogg\ContentRenderer\Asset;


interface AssetHandlerInterface
{

    /**
     * @param         $asset
     * @param  array  $parameters
     * @return Asset
     */
    public function getAsset($asset,$parameters = []):Asset;
}