# content-renderer
[![Build Status](https://travis-ci.org/e-frogg/content-renderer.svg?branch=master)](https://travis-ci.org/e-frogg/content-renderer) [![Coverage](coverage.svg)](coverage)

## execute tests and report coverage
    mkdir -p build/logs/
    vendor/bin/phpunit
    vendor/bin/php-coverage-badger build/logs/clover.xml coverage.svg
