<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Bootstrap;

require_once dirname(__DIR__) . '/Bootstrap.php';
require_once __DIR__ . '/Buildable.php';

use DecodeLabs\Genesis;
use DecodeLabs\Genesis\Bootstrap;
use DecodeLabs\Genesis\Hub;
use RuntimeException;

class Seamless implements
    Bootstrap,
    Buildable
{
    public const string VendorDir = 'vendor';
    public const string RunDir = 'data/local/run';
    public const string BuildPrefix = 'build';
    public const string BuildEntry = 'entry.php';

    public const array SourceArguments = [
        '--fabric-source'
    ];

    public protected(set) string $hubClass;
    public protected(set) string $rootPath;

    public protected(set) string $runDir;
    public protected(set) string $buildPrefix;
    public protected(set) string $buildEntry;
    public string $buildStrategy { get => 'Seamless'; }

    public protected(set) string $rootVendorDir;
    public protected(set) string $buildVendorDir;

    /**
     * @param class-string<Hub> $hubClass
     */
    public function __construct(
        string $hubClass,
        ?string $rootPath = null,
        ?string $runDir = null,
        ?string $buildPrefix = null,
        ?string $buildEntry = null,
        ?string $rootVendorDir = null,
        ?string $buildVendorDir = null
    ) {
        $this->hubClass = $hubClass;

        if ($rootPath === null) {
            $rootPath = $this->findRootPath();
        }

        $this->rootPath = $rootPath;

        $this->runDir = $runDir ?? self::RunDir;
        $this->buildPrefix = $buildPrefix ?? self::BuildPrefix;
        $this->buildEntry = $buildEntry ?? self::BuildEntry;

        $this->rootVendorDir = $rootVendorDir ?? self::VendorDir;
        $this->buildVendorDir = $buildVendorDir ?? $this->rootVendorDir;
    }

    protected function findRootPath(): string
    {
        /** @var string $entryPath */
        $entryPath = $_SERVER['SCRIPT_FILENAME'] ?? '';

        /** @var non-empty-string|false $entryPath */
        $entryPath = realpath($entryPath);

        if ($entryPath === false) {
            throw new RuntimeException('Entry path could not be determined');
        }

        if (str_ends_with($entryPath, '/src/Bootstrap.php')) {
            return dirname($entryPath, 2);
        }

        throw new RuntimeException('Root path could not be determined');
    }

    public function initializeOnly(): void
    {
        $this->loadVendor(
            $this->getSearchPaths()
        );

        Genesis::bootstrap($this);
    }

    public function run(): never
    {
        $this->loadVendor(
            $this->getSearchPaths()
        );

        $kernel = Genesis::bootstrap($this);
        $kernel->run();
        $kernel->shutdown();
    }

    /**
     * Search for vendor root in possible paths
     *
     * @param array<string,string> $paths
     */
    protected function loadVendor(
        array $paths
    ): string {
        foreach ($paths as $testFile => $vendorPath) {
            if (
                file_exists($testFile) &&
                file_exists($vendorPath . '/autoload.php')
            ) {
                require_once $testFile;
                require_once $vendorPath . '/autoload.php';
                return $vendorPath;
            }
        }

        throw new RuntimeException('No root vendor installation found');
    }

    /**
     * @return array<string,string>
     */
    protected function getSearchPaths(): array
    {
        $sourceMode = false;

        /** @var array<string> */
        $args = $_SERVER['argv'] ?? [];

        foreach ($args as $arg) {
            if (in_array($arg, static::SourceArguments)) {
                $sourceMode = true;
                break;
            }
        }

        if (!$sourceMode) {
            $runDir = $this->runDir;

            if (str_starts_with($runDir, './')) {
                $runDir = substr($runDir, 2);
            }

            if (!str_starts_with($runDir, '/')) {
                $runDir = $this->rootPath . '/' . $runDir;
            }

            $paths = [
                $runDir . '/' . $this->buildPrefix . '1/' . $this->buildEntry => $runDir . '/' . $this->buildPrefix . '1/' . $this->buildVendorDir,
                $runDir . '/' . $this->buildPrefix . '2/' . $this->buildEntry => $runDir . '/' . $this->buildPrefix . '2/' . $this->buildVendorDir,
            ];
        } else {
            $paths = [];
        }

        $paths[__FILE__] = $this->rootPath . '/' . $this->rootVendorDir;
        return $paths;
    }
}
