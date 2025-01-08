<?php

namespace Efrogg\ContentRenderer\Cache;

use Efrogg\ContentRenderer\Converter\NodeToArrayConverter;
use Efrogg\ContentRenderer\DependencyInjection\ContentRendererConfig;
use Efrogg\ContentRenderer\Log\LoggerProxy;
use Efrogg\ContentRenderer\Node;
use Psr\Log\LoggerInterface;

/**
 * PSR-6 compliant implementation of RedisPersister
 * Class RedisCache.
 */
class JsonDumperCache extends DummyCache implements CacheKeyEncoderInterface
{
    use LoggerProxy;

    protected string $baseStoragePath;

    private NodeToArrayConverter $converter;

    public function __construct(ContentRendererConfig $config, ?LoggerInterface $logger = null)
    {
        parent::__construct();
        if ($logger) {
            $this->setLogger($logger);
        }

        $this->converter = new NodeToArrayConverter();
        $this->baseStoragePath = rtrim($config->getCacheJsonPath(), '/');
    }

    /**
     * @param array<mixed>|null $metadata
     */
    public function get(string $key, callable $callback, ?float $beta = null, ?array &$metadata = null): mixed
    {
        /** @var Node $node */
        $node = parent::get($key, $callback, $beta, $metadata);

        // ne pas sauvegarder le json en mode preview
        if ($node->isPreview()) {
            $this->info('no save cache because of preview mode is enabled');

            return $node;
        }

        $data = $this->converter->convert($node);
        try {
            $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        } catch (\JsonException $e) {
            $this->error('unable to convert to json', ['data' => $data]);

            return $node;
        }
        $finalStorageFile = $this->getStorageFilename($key);

        // création du dossier, le cas échéant
        $dir = dirname($finalStorageFile);
        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            $this->error('Directory "%s" was not created', ['dir' => $dir]);

            return $node;
        }

        // sauvegarde du fichier json
        $saved = file_put_contents($finalStorageFile, $json);
        if (false === $saved) {
            $this->error('could not write file '.$finalStorageFile);
        }

        $this->info('saved json ('.$finalStorageFile.')', ['fileName' => $finalStorageFile, 'data' => $json, 'title' => 'JsonDumperNodeProvider']);

        return $node;
    }

    public function delete(string $key): bool
    {
        $finalStorageFile = $this->getStorageFilename($key);

        return !(file_exists($finalStorageFile) && !unlink($finalStorageFile));
    }

    protected function getStorageFilename(string $key): string
    {
        return $this->baseStoragePath.'/'.$key.'.json';
    }

    public function encodeKey(string $nodeIdWithPrefix): string
    {
        // keep here original node id (path)
        return $nodeIdWithPrefix;
    }
}
