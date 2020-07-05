<?php


namespace Efrogg\ContentRenderer\DataProvider;


trait DataProviderAwareTrait
{
    /**
     * @var DataProviderResolver
     */
    protected $dataProviderResolver;

    /**
     * @param  DataProviderResolver  $dataProviderResolver
     * @return static
     */
    public function setDataProviderResolver(DataProviderResolver $dataProviderResolver): self
    {
        $this->dataProviderResolver = $dataProviderResolver;

        return $this;
    }

    /**
     * @return DataProviderResolver
     */
    public function getDataProviderResolver(): ?DataProviderResolver
    {
        return $this->dataProviderResolver;
    }

    /**
     * @param  string  $dataType
     * @return DataProviderInterface
     * @throws DataProviderNotFoundException
     */
    protected function getDataProviderFor(string $dataType): DataProviderInterface
    {
        if (null !== $this->getDataProviderResolver()) {
            return $this->getDataProviderResolver()->resolve($dataType);
        }

        throw new DataProviderNotFoundException('no DataProviderManager');
    }

    protected function hasDataProviderFor(string $dataType): bool
    {
        try {
            return $this->getDataProviderFor($dataType) instanceof DataProviderInterface;
        } catch (DataProviderNotFoundException $e) {
            return false;
        }
    }
}