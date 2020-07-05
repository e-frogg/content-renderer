<?php


namespace Efrogg\ContentRenderer\DataProvider;


interface DataProviderAwareInterface
{
    public function setDataProviderResolver(DataProviderResolver $dataProviderManager);
}