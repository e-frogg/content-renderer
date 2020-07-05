<?php


namespace Efrogg\ContentRenderer\Decorator;


trait DecoratorAwareTrait
{
    /**
     * @var DecoratorInterface[]
     */
    private $decorators = [];

    public function addDecorator(DecoratorInterface $nodeDecorator): void
    {
        $this->decorators[] = $nodeDecorator;
    }

    public function getDecorators():array
    {
        return $this->decorators;
    }

    /**
     * @param  mixed  $data
     * @return mixed
     */
    public function decorate($data)
    {
        foreach ($this->decorators as $decorator) {
            $data = $decorator->decorate($data);
        }
        return $data;
    }

}