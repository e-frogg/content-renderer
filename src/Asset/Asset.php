<?php
/** @noinspection PhpMissingParentConstructorInspection */


namespace Efrogg\ContentRenderer\Asset;


use Efrogg\ContentRenderer\Converter\Keyword;
use Efrogg\ContentRenderer\Node;

/**
 * Asset is solvable for AssetHandler via AssetResolver
 * Class Asset
 * @package Efrogg\ContentRenderer\Asset
 * @method $this setSrc(string $src)
 * @method string getSrc()
 *
 * @method $this setParameters(array $parameters)
 * @method array getParameters()
 */
class Asset extends Node
{

    public function __construct(?array $data = null)
    {
        parent::__construct([Keyword::NODE_TYPE=>Keyword::TYPE_ASSET],[],$data);
    }

    public static function _factorySrc($src): Asset
    {
        return new self(['src' => $src]);
    }

    /**
     * used to convert asset
     * @return mixed
     */
    public function export()
    {
        return $this->getData();
    }

    /**
     * @return mixed|null
     */
    public function getAssetId()
    {
        // par défaut, même id
        return $this->getNodeId();
    }

}