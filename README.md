# Genesis

[![PHP from Packagist](https://img.shields.io/packagist/php-v/decodelabs/genesis?style=flat)](https://packagist.org/packages/decodelabs/genesis)
[![Latest Version](https://img.shields.io/packagist/v/decodelabs/genesis.svg?style=flat)](https://packagist.org/packages/decodelabs/genesis)
[![Total Downloads](https://img.shields.io/packagist/dt/decodelabs/genesis.svg?style=flat)](https://packagist.org/packages/decodelabs/genesis)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/decodelabs/genesis/integrate.yml?branch=develop)](https://github.com/decodelabs/genesis/actions/workflows/integrate.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-44CC11.svg?longCache=true&style=flat)](https://github.com/phpstan/phpstan)
[![License](https://img.shields.io/packagist/l/decodelabs/genesis?style=flat)](https://packagist.org/packages/decodelabs/genesis)

### Universal bootstrapping framework for PHP applications

Genesis provides everything you need to bootstrap your application at runtime. Take the guesswork out of how best to structure the lowest level code in your framework and app.

---


## Installation

Install via Composer:

```bash
composer require decodelabs/genesis
```

## Usage

### Hub

Genesis requires consumers of the library to implement a number of interfaces to represent important parts of the bootstrap process. With these classes in place, it is then able to provide a unified, dependable bootstrap process for all environments.

Most important is the `Hub` interface:

```php
namespace DecodeLabs\Genesis;

use DecodeLabs\Genesis\Environment\Config as EnvConfig;
use DecodeLabs\Genesis\Build\Manifest as BuildManifest;
use DecodeLabs\Kingdom;

interface Hub
{
    public ?BuildManifest $buildManifest { get; }

    public function initializeLoaders(): void;
    public function loadBuild(): Build;
    public function loadEnvironmentConfig(): EnvConfig;
    public function initializePlatform(): void;
    public function loadKingdom(): Kingdom;
}
```


## Bootstrapping

`Genesis` now runs as a composer plugin, automatically generating an entry point for your application when composer updates.

You just need to add make sure you have set your composer `type` ('project' for a standalone app, 'library' for a library) and your `Hub` class to the `genesis.hub` extra key in your `composer.json` file:

```json
{
    "extra": {
        "genesis": {
            "hub": "My\\Genesis\\Hub"
        }
    }
}
```

You can then point your HTTP server to rewite to `vendor/genesis.php` as your entry point. Genesis takes care of the rest.


## Compiled builds

Genesis supports an advanced build compilation process which can be used for isolating active runtime code from the source of your application. This is especially useful for legacy frameworks that can't easily be deployed using a third party automated deployment system.

Compiled builds are a complex topic due to the necessity of locating the correct build folder before loading _any_ other code and needing to seamlessly deploy updates without unwittingly mixing different versions of libraries during execution.

Full details of [how to work with compiled builds can be found here](docs/builds.md).

## Licensing

Genesis is licensed under the MIT License. See [LICENSE](./LICENSE) for the full license text.
