<?php


namespace Efrogg\ContentRenderer\NodeProvider;


use Efrogg\ContentRenderer\Converter\NodeToArrayConverter;
use Efrogg\ContentRenderer\Decorator\DecoratorInterface;
use Efrogg\ContentRenderer\Exception\NodeNotFoundException;
use Efrogg\ContentRenderer\Log\LoggerProxy;
use Efrogg\ContentRenderer\Node;
use LogicException;

class JsonDumperNodeProvider implements NodeProviderInterface, CachedNodeProviderInterface
{
    use NodeProviderAwareTrait;
    use LoggerProxy;

    /**
     * @var string
     */
    protected $baseStoragePath;

    /**
     * @var NodeToArrayConverter
     */
    private $converter;

    public function __construct(string $baseStoragePath)
    {
        $this->converter = new NodeToArrayConverter();
        // TODO : dans le containerBuilder
        $this->baseStoragePath = rtrim($baseStoragePath,'/');
    }

    public function addConverterDecorator(DecoratorInterface $decorator): void
    {
        $this->converter->addDecorator($decorator);
    }


    public function addDecorator(DecoratorInterface $decorator): void
    {
        $this->getNodeProvider()->addDecorator($decorator);
    }

    public function getDecorators(): array
    {
        return $this->getNodeProvider()->getDecorators();
    }

    /**
     * @param string $nodeId
     * @return Node
     * @throws NodeNotFoundException
     * @throws LogicException
     */
    public function getNodeById(string $nodeId): Node
    {
            $node = $this->getNodeProvider()->getNodeById($nodeId);
            // ne pas sauvegarder le json en mode preview
            if($node->isPreview()) {
                $this->info('no save cache because of preview mode is enabled');
                return $node;
            }

            $data = $this->converter->convert($node);
            $json = json_encode($data, JSON_PRETTY_PRINT);
            if (false === $json) {
                $this->error('unable to convert to json', ['data' => $data]);
                return $node;
            }
        $finalStorageFile = $this->getFileNameById($nodeId);

            // création du dossier, le cas échéant
            $dir = dirname($finalStorageFile);
            if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
                $this->error('Directory "%s" was not created', ['dir' => $dir]);
                return $node;
            }

            // sauvegarde du fichier json
            $saved = file_put_contents($finalStorageFile, $json);
            if (false === $saved) {
                $this->error('could not write file ' . $finalStorageFile);
            }
        $this->info('saved json ', ['fileName' => $finalStorageFile, 'data' => $json, 'title' => 'JsonDumperNodeProvider']);
        return $node;
    }

    public function canResolve($solvable, string $resolverName): bool
    {
        return $this->getNodeProvider()->canResolve($solvable, $resolverName);
    }

    public function clearCacheById(string $nodeId): bool
    {
        $finalStorageFile = $this->getFileNameById($nodeId);

        if (file_exists($finalStorageFile) && !unlink($finalStorageFile)) {
            return false;
        }
        return true;
    }

    public function setCacheActive(bool $cacheActive): void
    {
        // rien
    }

    public function setCacheReset(bool $cacheReset): void
    {
        // rien
    }

    /**
     * @param string $nodeId
     *
     * @return string
     */
    protected function getFileNameById(string $nodeId): string
    {
        return $this->baseStoragePath . '/' . $nodeId . '.json';
    }

}
