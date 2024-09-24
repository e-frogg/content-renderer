<?php


namespace Efrogg\ContentRenderer\Core;



class MagicObject implements \ArrayAccess
{
    /**
     * @var array<string,mixed>
     */
    private $data;

    /**
     * MagicObject constructor.
     * @param  array<string,mixed>  ...$datas
     */
    public function __construct(...$datas)
    {
        $this->setData(array_merge(...array_filter($datas)));
    }

    /**
     * @return array<string,mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param  array<string,mixed>  $data
     * @return self
     */
    public function setData(array $data): self
    {
//        pp($data);
        $this->data = $data;
        return $this;
    }


    public function __get(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }
    public function __set(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }

    public function __isset(string $name)
    {
        return isset($this->data[$name]);
    }

    public function __unset(string $name)
    {
        unset($this->data[$name]);
    }

    /**
     * @param array<mixed>  $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        if(str_starts_with($name, 'set')) {
            $property = lcfirst(substr($name,3));
            $this->__set($property,reset($arguments));
            return null;
        }

        if(str_starts_with($name, 'get')) {
            $property = lcfirst(substr($name,3));
            return $this->__get($property);
        }

        return null;
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->__isset($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->__get($offset);
    }

    public function offsetSet(mixed $offset, $value): void
    {
        $this->__set($offset,$value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->__unset($offset);
    }
}
