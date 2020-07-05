<?php


namespace Efrogg\ContentRenderer\DataProvider;


class GenericDataProvider implements DataProviderInterface
{

    /**
     * @var array
     */
    private $handledDataTypes;

    /**
     * @var mixed
     */
    private $data;

    /**
     * GenericDataProvider constructor.
     * @param  string[]  $handledDataTypes
     * @param  mixed     $data
     */
    public function __construct(array $handledDataTypes, $data)
    {
        $this->handledDataTypes = $handledDataTypes;
        $this->data = $data;
    }


    /**
     * @param  string  $solvable
     * @param  string  $resolverName
     * @return bool
     */
    public function canResolve($solvable, string $resolverName): bool
    {
        return in_array($solvable, $this->handledDataTypes, true);
    }

    public function getData()
    {
        return $this->data;
    }
}