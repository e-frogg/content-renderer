<?php


namespace Efrogg\ContentRenderer\Core;



class MagicObject implements \ArrayAccess
{
    /**
     * @var array
     */
    private $data;

    /**
     * MagicObject constructor.
     * @param  array[]  $datas
     */
    public function __construct(...$datas)
    {
        $this->setData(array_merge(...array_filter($datas)));
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param  array  $data
     * @return self
     */
    public function setData(array $data): self
    {
//        pp($data);
        $this->data = $data;
        return $this;
    }


    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    public function __call($name, $arguments)
    {
        if(strpos($name,'set')===0) {
            $property = lcfirst(substr($name,3));
            $this->__set($property,reset($arguments));
            return ;
        }

        if(strpos($name,'get')===0) {
            $property = lcfirst(substr($name,3));
            return $this->__get($property);
        }
    }

    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->__set($offset,$value);
    }

    public function offsetUnset($offset)
    {
        $this->__unset($offset);
    }
}