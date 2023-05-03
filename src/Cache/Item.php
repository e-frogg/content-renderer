<?php


namespace Efrogg\ContentRenderer\Cache;



use DateInterval;
use DateTimeInterface;
use Symfony\Contracts\Cache\ItemInterface;

class Item implements ItemInterface
{


    private string $key;

    private mixed $value;

    private bool $isHit = false;

    private DateInterval|int|null $expiresAfter;

    private ?DateTimeInterface $expiresAt;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function get(): mixed
    {
        return $this->value;
    }

    public function isHit(): bool
    {
        return $this->isHit;
    }

    public function set($value): static
    {
        $this->value = $value;
        return $this;
    }

    public function expiresAt($expiration): static
    {
        $this->expiresAt = $expiration;
        return $this;
    }

    public function expiresAfter($time): static
    {
        $this->expiresAfter = $time;
        return $this;
    }

    public function getTTL(): int|null
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

    /**
     * @return array<mixed>
     */
    public function getMetadata(): array
    {
        return [];
    }

    /**
     * @param  bool  $isHit
     * @return static
     */
    public function setIsHit(bool $isHit): static
    {
        $this->isHit = $isHit;
        return $this;
    }
}
