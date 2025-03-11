<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

use Exception;

abstract class Bootstrap
{
    /**
     * Bootstrap and run the app
     */
    final public function run(): void
    {
        // Lookup best choice vendor path
        $vendorPath = $this->findRoot(
            $this->getRootSearchPaths()
        );

        $this->execute($vendorPath);
    }


    /**
     * Search for vendor root in possible paths
     *
     * @param array<string,string> $paths
     */
    final public function findRoot(
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

        throw new Exception('No root vendor installation found');
    }


    /**
     * Default execution method
     */
    public function execute(
        string $vendorPath
    ): void {
    }

    /**
     * @return array<string,string>
     */
    abstract public function getRootSearchPaths(): array;
}
