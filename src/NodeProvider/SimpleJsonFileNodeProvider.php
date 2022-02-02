<?php


namespace Efrogg\ContentRenderer\NodeProvider;


use Efrogg\ContentRenderer\Converter\JsonConverter;
use Efrogg\ContentRenderer\Core\Resolver\SortableSolverInterface;
use Efrogg\ContentRenderer\Core\Resolver\SortableSolverTrait;
use Efrogg\ContentRenderer\Decorator\DecoratorInterface;
use Efrogg\ContentRenderer\Exception\InvalidDataException;
use Efrogg\ContentRenderer\Exception\InvalidJsonException;
use Efrogg\ContentRenderer\Exception\NodeNotFoundException;
use Efrogg\ContentRenderer\Log\LoggerProxy;
use Efrogg\ContentRenderer\Node;
use LogicException;
use Psr\Log\LoggerAwareInterface;

class SimpleJsonFileNodeProvider implements NodeProviderInterface, LoggerAwareInterface, SortableSolverInterface
{
    use CacheableNodeProviderTrait;
    use LoggerProxy;
    use SortableSolverTrait;
    /**
     * @var string|null
     */
    protected $rootPath;
    /**
     * @var string
     */
    private $extension;
    /**
     * @var JsonConverter
     */
    private $converter;

    /**
     * SimpleJsonFileDataProvider constructor.
     * @param  string|null  $rootPath
     * @param  string       $extension
     */
    public function __construct(string $rootPath=null,string $extension='.json')
    {
        $this->converter = new JsonConverter();

        if(null !== $rootPath) {
            $this->setRootPath($rootPath);
        }
        if(null !== $extension) {
            $this->setExtension($extension);
        }
    }

    /**
     * @param  string  $nodeId
     * @return Node
     * @throws NodeNotFoundException
     * @throws InvalidDataException
     * @throws InvalidJsonException
     * @throws LogicException
     */
    public function fetchNodeById(string $nodeId): Node
    {
        return $this->converter->convert($this->getNodeJson($nodeId));
    }

    public function canResolve($solvable, string $resolverName): bool
    {
        return file_exists($this->getFilePath($solvable));
    }

    /**
     * @param  string  $nodeId
     * @return string
     * @throws NodeNotFoundException
     */
    private function getNodeJson(string $nodeId)
    {
        $filePath = $this->getFilePath($nodeId);
        if(!file_exists($filePath)) {
            $this->info("file does not exist : ".$filePath,['title'=>'SimpleJsonFileNodeProvider']);
            throw new NodeNotFoundException('node "'.$nodeId.'" was not found');
        }

        $this->info("load file : ".$filePath,['title'=>'SimpleJsonFileNodeProvider']);
        return file_get_contents($filePath);
    }

    /**
     * @param  string  $nodeId
     * @return string
     */
    public function getFilePath(string $nodeId): string
    {
        return $this->getRootPath().$nodeId.$this->getExtension();
    }

    /**
     * @param  string|null  $rootPath
     * @return SimpleJsonFileNodeProvider
     */
    public function setRootPath(?string $rootPath): SimpleJsonFileNodeProvider
    {
        $this->rootPath = rtrim($rootPath,'/').'/';
        return $this;
    }

    /**
     * @return string
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * @param  string  $extension
     * @return SimpleJsonFileNodeProvider
     */
    public function setExtension(string $extension): SimpleJsonFileNodeProvider
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    public function addDecorator(DecoratorInterface $decorator): void
    {
        $this->converter->addDecorator($decorator);
    }

    public function getDecorators(): array
    {
        return $this->converter->getDecorators();
    }

    public function getCacheKeyPrefix(): string
    {
        return 'cms.json.';
    }
}
