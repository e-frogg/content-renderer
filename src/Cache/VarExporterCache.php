<?php


namespace Efrogg\ContentRenderer\Cache;


use Psr\Cache\CacheItemInterface;
use Symfony\Component\VarExporter\VarExporter;
use Symfony\Contracts\Cache\CacheTrait;

/**
 * PSR-6 compliant implementation of RedisPersister
 * Class RedisCache
 */
class VarExporterCache extends AbstractContentCache
{
    use CacheTrait;

    private $storagePath;

    public function __construct($storagePath)
    {
        $this->storagePath = rtrim($storagePath, '/');
    }

    /**
     * @inheritDoc
     */
    public function getItem($key)
    {
        return $this->contentGetCacheItem($key,function($key,Item $item) {
            $object = require $this->getFileName($key);
            $this->debug('loaded ('.$key.')', $object);
            $item->set($object);
        });

    }

    public function getItems(array $keys = array())
    {
        return array_map([$this,'getItem'],$keys);
    }

    public function hasItem($key): bool
    {
        if (!$this->isUseCache()) {
            return false;
        }
        return file_exists($this->getFileName($key));
    }

    public function clear(): bool
    {
        // TODO: Implement clear() method.
    }

    public function deleteItem($key): bool
    {
        $this->debug('delete ('.$key.')'.$this->getFileName($key));
        if (file_exists($filename = $this->getFileName($key))) {
            if (!@unlink($filename)) {
                return false;
            }
        }
        return true;
    }

    public function deleteItems(array $keys): bool
    {
        return (bool)min(array_map([$this, 'delete'], $keys));
    }

    public function save(CacheItemInterface $item): bool
    {
        if (!$this->isUseCache()) {
            return true;
        }
        $exported = VarExporter::export($item->get());
        // TODO : TTL
        $fileName = $this->getFileName($item->getKey());
        $dirName = dirname($fileName);
        if (!is_dir($dirName)) {
            if (!mkdir($dirName, 0777, true) && !is_dir($dirName)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $dirName));
            }
        }
        $this->debug('save ('.$item->getKey().')',$item->get());
        return (bool)file_put_contents($fileName,'<?php return '.$exported.';');
    }

    public function saveDeferred(CacheItemInterface $item)
    {
        return $this->save($item);
        // TODO: Implement saveDeferred() method.
    }

    public function commit()
    {
        // no deffered here ...
        return true;
        // TODO: Implement commit() method.
    }

    private function getFileName(string $key)
    {
        return $this->storagePath.'/'.md5($key).'.php';
    }

}
