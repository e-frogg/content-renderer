<?php

namespace Efrogg\ContentRenderer\Converter;

use Efrogg\ContentRenderer\Asset\Asset;
use Efrogg\ContentRenderer\Decorator\DecoratorAwareTrait;
use Efrogg\ContentRenderer\Exception\InvalidDataException;
use Efrogg\ContentRenderer\Node;

class ArrayConverter implements ConverterInterface
{
    use DecoratorAwareTrait;
    /**
     * @var callable
     */
    private $nodeTypeResolver;

    /**
     * @param array $nodeData
     *
     * @return Node|Asset|array<Node|Asset>
     *
     * @throws InvalidDataException
     * @throws \LogicException
     */
    public function convert($nodeData)
    {
        if (!is_array($nodeData)) {
            throw new InvalidDataException(sprintf('ArrayConverter : data must be array. %s given', gettype($nodeData)));
        }

        $this->tryFixNodeType($nodeData);

        if (isset($nodeData[Keyword::NODE_TYPE])) {
            $nodeType = $nodeData[Keyword::NODE_TYPE];
            $data = [];
            foreach ($nodeData as $key => $value) {
                if (0 === strpos($key, '_')) {
                    $data[$key] = $value;
                    continue;
                }
                $data[$key] = $this->computeData($value);
            }

            if (Keyword::TYPE_ASSET === $nodeType) {
                return new Asset($data);
            }

            return new Node($data);
        }

        // simpleArray
        return array_map([$this, 'convert'], $nodeData);
    }

    /**
     * @return array|Node
     *
     * @throws InvalidDataException
     * @throws \LogicException
     */
    private function computeData($value)
    {
        // tableau de nodes
        if (is_array($value)) {
            $this->tryFixNodeType($value);

            if (isset($value[Keyword::NODE_TYPE])) {
                return $this->convert($value);
            }

            // tableau de nodes ou de valeurs
            return array_map([$this, 'computeData'], $value);
        }

        // une valeur
        return $this->decorate($value);
    }

    public function setNodeTypeResolver(callable $nodeTypeResolver): void
    {
        $this->nodeTypeResolver = $nodeTypeResolver;
    }

    private function tryFixNodeType(array &$nodeData): void
    {
        if (!isset($nodeData[Keyword::NODE_TYPE]) && isset($this->nodeTypeResolver)) {
            if (null !== ($computedNodeType = call_user_func($this->nodeTypeResolver, $nodeData))) {
                $nodeData[Keyword::NODE_TYPE] = $computedNodeType;
            }
        }
    }
}
