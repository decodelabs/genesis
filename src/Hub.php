<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

use DecodeLabs\Fluidity\Cast;
use DecodeLabs\Genesis\Build\Manifest as BuildManifest;
use DecodeLabs\Genesis\Environment\Config as EnvConfig;
use DecodeLabs\Genesis\Loader\Stack as StackLoader;

interface Hub extends Cast
{
    public ?BuildManifest $buildManifest { get; }

    /**
     * Init with options
     */
    public function __construct(
        Context $context,
        Bootstrap $bootstrap
    );

    /**
     * Register custom loaders into the stack
     */
    public function initializeLoaders(
        StackLoader $loader
    ): void;

    /**
     * Load build info
     */
    public function loadBuild(): Build;

    /**
     * Load object to convey environment configuration
     */
    public function loadEnvironmentConfig(): EnvConfig;

    /**
     * Setup platform libraries
     */
    public function initializePlatform(): void;

    /**
     * Load kernel to handle running the app
     */
    public function loadKernel(): Kernel;
}
