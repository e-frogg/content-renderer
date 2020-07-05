<?php


namespace Efrogg\ContentRenderer\Converter;


use Efrogg\ContentRenderer\Decorator\DecoratorInterface;
use Efrogg\ContentRenderer\Exception\InvalidDataException;
use Efrogg\ContentRenderer\Exception\InvalidJsonException;
use Efrogg\ContentRenderer\Node;
use LogicException;

class JsonConverter implements ConverterInterface
{
    /**
     * @var ArrayConverter
     */
    private $arrayConverter;

    public function __construct()
    {
        $this->arrayConverter = new ArrayConverter();
    }

    /**
     * @param  string  $json
     * @return Node
     * @throws InvalidJsonException
     * @throws LogicException
     * @throws InvalidDataException
     */
    public function convert($json): Node
    {
        if(!is_string($json)) {
            throw new LogicException('JsonConverter : data must be a string');
        }

        $nodeData = json_decode($json, true, 512);
        if(null === $nodeData) {
            throw new InvalidJsonException('json is invalid : '.json_last_error().' : '.json_last_error_msg());
        }

//      PHP 7.3+
//            try {
//                $nodeData = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
//            } catch (\JsonException $e) {
//                throw new InvalidJsonException('json is invalid');
//            }



        return $this->arrayConverter->convert($nodeData);
    }

    public function addDecorator(DecoratorInterface $decorator): void
    {
        $this->arrayConverter->addDecorator($decorator);
    }

    public function getDecorators(): array
    {
        return $this->arrayConverter->getDecorators();
    }
}