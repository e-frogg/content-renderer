<?php


namespace Efrogg\ContentRenderer\NodeProvider;


class DemoNodeProvider extends SimpleJsonFileNodeProvider
{
    protected $rootPath=__DIR__.'/../../demo/data/';
}