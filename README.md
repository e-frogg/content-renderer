
# content-renderer
[![Build Status](https://travis-ci.org/e-frogg/content-renderer.svg?branch=master)](https://travis-ci.org/e-frogg/content-renderer) [![Coverage](coverage.svg)](coverage)

## Install on symfony

    composer require efrogg/content-renderer

### add a node provider
by default, there is no node provider. If you want to use "renderNodeById" method, you must have at least one node provider. 

    # config/services.yaml
    services:
        baseNodeProvider:
            class: 'Efrogg\ContentRenderer\NodeProvider\SimpleJsonFileNodeProvider'
            arguments:
                - '%kernel.project_dir%/cms/pages'
            tags: [!php/const Efrogg\ContentRenderer\Core\Resolver\ContainerTag::TAG_NODE_PROVIDER]

### add a module renderer
By default, there is a twig module renderer. It will look for a twig template, depending on the node type.

For example, a node typed "button" will look for the template "button.twig". Base twig namespace and file extension
can be configured using parameters listed below. 

### add decorator
```yaml
# config/services.yaml
services:
    Efrogg\ContentRenderer\NodeProvider\NodeProviderResolver:
        calls:
            - ['addDecorator',['@Efrogg\ContentRenderer\Decorator\UTF8DecodeStringDecorator']]
```

### add cache to the node provider

#### 1 : using native Symfony cache
```yaml
parameters:
    # use the cache pool name here
    cms.cache.service: 'cache.cms'
    # define here where php cache files will be stored
    storyblok.cache.storage-path: '/tmp'

```
cache pool definition example
 ```yaml
 # config/packages/cache.yaml
framework:
    cache:
        pools:
            cache.cms:
                adapter: 'cache.adapter.redis'
```
#### 2 : using provided custom cache adapter 
```yaml
# parameters.yaml
parameters:
    cms.cache.service: 'cms.cache.php'
    # define the right path
    storyblok.cache.storage-path: '/tmp'
```

### configure
Here is the list of usable parameters

    # config/services.yaml
    parameters:
        cms.twig.namespace: 'cms'
        cms.twig.extension: '.html.twig'

## execute tests and report coverage
Note : does not work anymore... waiting for a better working solution

    composer install
    mkdir -p build/logs/
    vendor/bin/phpunit
    vendor/bin/php-coverage-badger build/logs/clover.xml coverage.svg

