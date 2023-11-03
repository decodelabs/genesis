<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Build;

use DecodeLabs\Atlas\Dir;
use DecodeLabs\Atlas\File;
use DecodeLabs\Terminus\Session;
use Generator;

interface Manifest
{
    public function getCliSession(): Session;
    public function generateBuildId(): string;

    public function getBuildTempDir(): Dir;

    public function getRunDir(): Dir;
    public function getRunName1(): string;
    public function getRunName2(): string;
    public function getEntryFileName(): string;

    /**
     * @return Generator<Task>
     */
    public function scanPreCompileTasks(): Generator;

    /**
     * @return Generator<Package>
     */
    public function scanPackages(): Generator;

    /**
     * @return Generator<File|Dir, string>
     */
    public function scanPackage(
        Package $package
    ): Generator;

    public function writeEntryFile(
        File $file
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
