<?php

use Efrogg\ContentRenderer\Asset\Asset;
use Efrogg\ContentRenderer\Converter\ArrayConverter;
use Efrogg\ContentRenderer\Converter\JsonConverter;
use Efrogg\ContentRenderer\Converter\Keyword;
use Efrogg\ContentRenderer\Converter\NodeToArrayConverter;
use Efrogg\ContentRenderer\Decorator\UTF8DecodeStringDecorator;
use Efrogg\ContentRenderer\Exception\InvalidDataException;
use Efrogg\ContentRenderer\Exception\InvalidJsonException;
use Efrogg\ContentRenderer\Node;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    public function testNotJsonError()
    {
        $converter = new JsonConverter();
        $this->expectException(LogicException::class);
        $converter->convert(123);
    }
    public function testInvalidJsonError()
    {
        $converter = new JsonConverter();
        $this->expectException(InvalidJsonException::class);
        $converter->convert('this is not a valid json');
    }
    public function testNoArrayError()
    {
        $converter = new ArrayConverter();
        $this->expectException(InvalidDataException::class);
        $converter->convert('hello');
    }

    public function testInvalidTypeError()
    {
        $converter = new ArrayConverter();
        $this->expectException(InvalidDataException::class);
        $converter->convert(['key' => 'value']);
    }

    public function testConvert()
    {
        $converter = new ArrayConverter();
        $node = $converter->convert(
            [
                'key'              => 'value',
                'count'              => 7,
                Keyword::NODE_TYPE => 'testType',
                'assets'           => [
                    [
                        '_type' => Keyword::TYPE_ASSET,
                        'src'   => 'image1.jpg'
                    ],
                    [
                        '_type' => Keyword::TYPE_ASSET,
                        'src'   => 'image2.jpg'
                    ]
                ]
            ]
        );

        self::assertInstanceOf(Node::class, $node);
        self::assertEquals('testType',$node->getType());
        self::assertIsString($node->key);
        self::assertIsInt($node->count);
        self::assertEquals('value',$node->key);
        self::assertCount(2,$node->assets);
        self::assertInstanceOf(Asset::class,$node->assets[0]);
        self::assertInstanceOf(Asset::class,$node->assets[1]);
        self::assertEquals('image1.jpg',$node->assets[0]->src);

        $resverseConverter = new NodeToArrayConverter();
        $array = $resverseConverter->convert($node);
        self::assertIsArray($array);
        self::assertIsString($array['key']);
        self::assertEquals('value',$array['key']);
        self::assertIsInt($array['count']);

        self::assertCount(2,$array['assets']);
        self::assertEquals('image2.jpg',$array['assets'][1]['src']);
    }

    public function testDecorator() {
        $converter = new JsonConverter();
        self::assertCount(0,$converter->getDecorators());

        $converter->addDecorator(new UTF8DecodeStringDecorator());
        self::assertCount(1,$converter->getDecorators());

        $node = $converter->convert('{
            "_type":"test",
            "key":"dédé"
        }');
        self::assertStringContainsString(utf8_decode('dédé'),$node->key);
    }

}
