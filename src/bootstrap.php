<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

use Exception;

final class Bootstrap
{
    /**
     * Bootstrap and run the app
     */
    public function run(
        BootstrapHandler $handler
    ): void {
        // Lookup best choice vendor path
        $vendorPath = $this->findRoot(
            $handler->getRootSearchPaths()
        );

        $handler->execute($vendorPath);
    }


    /**
     * Search for vendor root in possible paths
     *
     * @param array<string, string> $paths
     */
    public function findRoot(array $paths): string
    {
        foreach ($paths as $testFile => $vendorPath) {
            if (file_exists($testFile)) {
                require_once $testFile;

                if (!file_exists($vendorPath . '/autoload.php')) {
                    throw new Exception('No autoload.php found under ' . $vendorPath);
                }

                require_once $vendorPath . '/autoload.php';
                return $vendorPath;
            }
        }

        throw new Exception('No root vendor installation found');
    }
}

interface BootstrapHandler
{
    /**
     * @return array<string, string>
     */
    public function getRootSearchPaths(): array;

    public function execute(string $vendorPath): void;
}
