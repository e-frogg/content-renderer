<?php


namespace Efrogg\ContentRenderer\Core\Resolver;


interface ContainerTag
{
    public const TAG_MODULE_RENDERER = 'cms.module_renderer';
    public const TAG_MODULE = 'cms.module';
    public const TAG_NODE_PROVIDER = 'cms.node_provider';
    public const TAG_ASSET_HANDLER = 'cms.asset_handler';
    public const TAG_DATA_PROVIDER = 'cms.data_provider';
    public const TAG_CACHE_CLEAR_ON_PUBLISH = 'cms.cache_clear.on_publish';
    public const TAG_CACHE_CLEAR_ON_UNPUBLISH = 'cms.cache_clear.on_unpublish';
}
