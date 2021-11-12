<?php


namespace Efrogg\ContentRenderer\Cache;


use Efrogg\ContentRenderer\Event\CacheEvent;
use Efrogg\ContentRenderer\Event\CmsEventDispatcher;
use Efrogg\ContentRenderer\Node;
use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\VarExporter\VarExporter;
use Symfony\Contracts\Cache\CacheTrait;

/**
 * PSR-6 compliant implementation of RedisPersister
 * Class RedisCache
 */
class VarExporterCache extends AbstractContentCache
{
    use CacheTrait;

    /**
     * @var CmsEventDispatcher
     */
    protected $cmsEventDispatcher;
    private $storagePath;

    public function __construct($storagePath, CmsEventDispatcher $cmsEventDispatcher,?LoggerInterface $logger = null)
    {
        $this->storagePath = rtrim($storagePath, '/');
        $this->initLogger($logger);
        $this->cmsEventDispatcher = $cmsEventDispatcher;
        $cmsEventDispatcher->addListener(CacheEvent::CACHE_CLEAR, [$this, 'onClear']);
    }

    /**
     * @inheritDoc
     */
    public function getItem($key)
    {
        return $this->contentGetCacheItem(
            $key,
            function ($key, Item $item) {
                /** @var Node $object */
                $object = require($fileName = $this->getFileName($key));
//            $this->debug('loaded ('.$key.')', $object);
                $this->info(
                    'loaded : "' . $key . '"',
                    [
                        'title' => 'VarExporterCache',
                        'file'  => $fileName,
                        'data'  => $object->getData()
                    ]
                );
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
        return true;
    }

    public function onClear(CacheEvent $nodeEvent): void
    {
        $this->debug('clear cache "'.$nodeEvent->getId().'"');

        $this->deleteItem($nodeEvent->getId());
    }

    public function deleteItem($key): bool
    {
        $this->info('delete "' . $key . '"', ['title' => 'VarExporterCache']);

        if (file_exists($filename = $this->getFileName($key))) {
            $this->debug('delete file '.$filename);
            if (!@unlink($filename)) {
                return false;
            }
        } else {
            $this->debug('no file '.$filename);
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

        $this->cmsEventDispatcher->dispatch(new CacheEvent($item->getKey(),$item->get()),CacheEvent::CACHE_SAVE);

//        $this->debug('save ('.$item->getKey().')',$item->get());
        $this->info(
            'save "' . $item->getKey() . '"',
            [
                'title' => 'VarExporterCache',
                'data'  => $item->get()->getData()
            ]
        );
        return (bool)file_put_contents($fileName, '<?php return ' . $exported . ';');
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
        return $this->storagePath . '/' . md5($key) . '.php';
    }

}
