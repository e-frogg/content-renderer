<?php


namespace Efrogg\ContentRenderer\Decorator;


trait DecoratorAwareTrait
{
    /**
     * @var DecoratorInterface[]
     */
    private $decorators = [];

    public function addDecorator(DecoratorInterface $decorator): void
    {
        $this->decorators[] = $decorator;
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
