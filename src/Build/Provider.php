<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Build;

use DecodeLabs\Atlas\Dir;
use DecodeLabs\Atlas\File;
use Generator;

interface Provider
{
    public string $name { get; }

    public function __construct();

    /**
     * @return Generator<File|Dir,string>
     */
    public function scanBuildItems(
        Dir $rootDir
    ): Generator;
}
