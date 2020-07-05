<?php


namespace Efrogg\ContentRenderer;


use Symfony\Component\HttpFoundation\ParameterBag;

trait ParameterizableTrait
{
    /**
     * @var ParameterBag
     */
    private $parameters;

    public function addParameter($key, $value): void
    {
        $this->initParameters();
        $this->parameters->set($key, $value);
    }

    public function addParameters(array $keys): void
    {
        $this->initParameters();
        foreach ($keys as $key => $value) {
            $this->parameters->set($key, $value);
        }
    }

    /**
     * @return ParameterBag
     */
    public function getParameters(): ?ParameterBag
    {
        return $this->parameters;
    }

    /**
     * @param  ParameterBag  $parameters
     */
    public function setParameters(?ParameterBag $parameters): void
    {
        $this->parameters = $parameters;
    }

    protected function initParameters():void
    {
        if(null === $this->parameters) {
            $this->setParameters(new ParameterBag());
        }
    }

}