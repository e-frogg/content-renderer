<?php


namespace Efrogg\ContentRenderer\Converter;


use Efrogg\ContentRenderer\Asset\Asset;
use Efrogg\ContentRenderer\Asset\AssetResolver;
use Efrogg\ContentRenderer\Core\Resolver\Exception\InvalidSolvableException;
use Efrogg\ContentRenderer\Core\Resolver\Exception\SolverNotFoundException;
use Efrogg\ContentRenderer\Decorator\DecoratorAwareInterface;
use Efrogg\ContentRenderer\Decorator\DecoratorAwareTrait;
use Efrogg\ContentRenderer\Node;
use LogicException;

class NodeToArrayConverter implements NodeConterterInterface,DecoratorAwareInterface
{
    use DecoratorAwareTrait;

    private ?AssetResolver $assetResolver;

    /**
     * NodeToArrayConverter constructor.
     * @param  AssetResolver|null  $assetResolver
     */
    public function __construct(?AssetResolver $assetResolver = null)
    {
        $this->assetResolver = $assetResolver;
    }

    /**
     * @param  Node  $node
     * @return array<string,mixed>
     * @throws LogicException
     */
    public function convert(Node $node):array
    {
        $data = [
            Keyword::NODE_TYPE=>$node->getType()
        ];
        foreach ($node->getData() as $key => $value) {
            // les clés préfixées __ ne sont pas sauvegardées
            if(str_starts_with($key, '__')) {
                continue;
            }

            $data[$key] = $this->convertValue($value);
        }
        return $data;

    }

    /**
     * @param $value
     * @return array|mixed
     * @throws LogicException
     */
    private function convertValue($value)
    {
        if($value instanceof Asset) {
            return $this->prepareAsset($value)->export();
        }

        if($value instanceof Node) {
            return $this->convert($value);
        }


        if(is_iterable($value)) {
            $returnedValue=[];
            foreach ($value as $k => $oneValue) {
                $returnedValue[$k] = $this->convertValue($oneValue);
            }
            return $returnedValue;
        }

        if(is_object($value)) {
            throw new LogicException('unable ton convert '.get_class($value).' to array');
        }

        return $this->decorate($value);
    }

    private function prepareAsset(Asset $asset):Asset
    {
        if(null !== $this->assetResolver) {
            try {
                return $this->assetResolver->resolve($asset)->getAsset($asset);
            } catch (InvalidSolvableException $e) {
            } catch (SolverNotFoundException $e) {
            }
        }
        return $asset;
    }
}
