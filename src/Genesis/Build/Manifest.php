<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Build;

use DecodeLabs\Atlas\Dir;
use DecodeLabs\Atlas\File;
use DecodeLabs\Genesis\Hub;
use DecodeLabs\Terminus\Session;
use Generator;

interface Manifest
{
    public Strategy $strategy { get; }

    public function getCliSession(): Session;
    public function generateBuildId(): string;
    public function getBuildTempDir(): Dir;

    /**
     * @return Generator<Task>
     */
    public function scanPreCompileTasks(): Generator;

    /**
     * @return Generator<Package>
     */
    public function scanPackages(): Generator;

    /**
     * @return Generator<File|Dir,string>
     */
    public function scanPackage(
        Package $package
    ): Generator;

    /**
     * @param class-string<Hub> $hubClass
     */
    public function writeEntryFile(
        File $file,
        string $buildId,
        string $hubClass
    ): void;

    /**
     * @return Generator<Task>
     */
    public function scanPostCompileTasks(): Generator;

    /**
     * @return Generator<Task>
     */
    public function scanPostActivationTasks(): Generator;
}
