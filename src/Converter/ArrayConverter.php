<?php


namespace Efrogg\ContentRenderer\Converter;


use Efrogg\ContentRenderer\Asset\Asset;
use Efrogg\ContentRenderer\Decorator\DecoratorAwareTrait;
use Efrogg\ContentRenderer\Exception\InvalidDataException;
use Efrogg\ContentRenderer\Node;
use LogicException;

class ArrayConverter implements ConverterInterface
{
    use DecoratorAwareTrait;
    /**
     * @param $nodeData
     * @return Node|Asset
     * @throws InvalidDataException
     * @throws LogicException
     */
    public function convert($nodeData)
    {
        if(!is_array($nodeData)) {
            throw new InvalidDataException('ArrayConverter : data must be array');
        }

//        if (isset($nodeData['_type'])) {
        if (isset($nodeData[Keyword::NODE_TYPE])) {
            $nodeType = $nodeData[Keyword::NODE_TYPE];
            $data = [];
            foreach ($nodeData as $key => $value) {
                if(strpos($key,'_')===0) {
                    $data[$key] = $value;
                    continue;
                }
                $data[$key] = $this->computeData($value);
            }

            if($nodeType === Keyword::TYPE_ASSET) {
                return new Asset($data);
            }

            return new Node($data);
        }

        throw new InvalidDataException('no _type was provided : ['.implode(',',array_keys($nodeData)).']');
    }

    /**
     * @param $value
     * @return array|Node
     * @throws InvalidDataException
     * @throws LogicException
     */
    private function computeData($value)
    {
        // tableau de nodes
        if (is_array($value)) {

            if(isset($value[Keyword::NODE_TYPE])) {
                return $this->convert($value);
            }

            // tableau de nodes ou de valeurs
            return array_map([$this, 'computeData'], $value);
        }

        // une valeur
        return $this->decorate($value);
    }
}