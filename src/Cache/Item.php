<?php


namespace Efrogg\ContentRenderer\Cache;



use DateInterval;
use DateTimeInterface;
use Symfony\Contracts\Cache\ItemInterface;

class Item implements ItemInterface
{

    private $key;
    /**
     * @var mixed
     */
    private $value;
    /**
     * @var bool
     */
    private $isHit = false;
    /**
     * @var DateInterval|int|null
     */
    private $expiresAfter;
    /**
     * @var DateTimeInterface|nul
     */
    private $expiresAt;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function get()
    {
        return $this->value;
    }

    public function isHit()
    {
        return $this->isHit;
    }

    public function set($value)
    {
        $this->value = $value;
    }

    public function expiresAt($expiration)
    {
        $this->expiresAt = $expiration;
    }

    public function expiresAfter($time)
    {
        $this->expiresAfter = $time;
        return $this;
    }

    public function getTTL()
    {
        if(is_int($this->expiresAfter)) {
            return $this->expiresAfter;
        }

        if($this->expiresAfter instanceof DateInterval) {
            // conversion DateInterval en secondes
            return date_create('@0')->add($this->expiresAfter)->getTimestamp();
        }
        if($this->expiresAt instanceof DateTimeInterface) {
            return $this->expiresAt->getTimestamp() - (new \DateTime())->getTimestamp();
        }
        return null;
    }

    public function tag($tags): ItemInterface
    {
        // TODO: Implement tag() method.
        return $this;
    }

    public function getMetadata(): array
    {
        return [];
    }

    /**
     * @param  bool  $isHit
     * @return static
     */
    public function setIsHit(bool $isHit): Item
    {
        $this->isHit = $isHit;
        return $this;
    }
}