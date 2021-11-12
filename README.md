
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

    # config/services.yaml
    services:
        Efrogg\ContentRenderer\NodeProvider\NodeProviderResolver:
            calls:
                - ['addDecorator',['@Efrogg\ContentRenderer\Decorator\UTF8DecodeStringDecorator']]


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

