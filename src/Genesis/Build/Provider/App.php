<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Build\Provider;

use DecodeLabs\Atlas\Dir;
use DecodeLabs\Genesis\Build\Provider;
use Generator;

class App implements Provider
{
    public string $name = 'app';

    public function __construct()
    {
    }

    public function scanBuildItems(
        Dir $rootDir
    ): Generator {
        yield $rootDir->getFile('composer.json') => 'composer.json';
        yield $rootDir->getFile('composer.lock') => 'composer.lock';
        yield $rootDir->getFile('package.json') => 'package.json';
        yield $rootDir->getDir('src') => 'src/';
        yield $rootDir->getDir('tests') => 'tests/';
        yield $rootDir->getDir('vendor') => 'vendor/';
    }
}
