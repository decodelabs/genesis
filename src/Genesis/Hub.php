<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

use DecodeLabs\Genesis\Build\Manifest as BuildManifest;
use DecodeLabs\Genesis\Environment\Config as EnvConfig;
use DecodeLabs\Genesis\Loader\Stack as StackLoader;

interface Hub
{
    /**
     * Init with options
     *
     * @param array<string, mixed> $options
     */
    public function __construct(
        Context $context,
        array $options
    );

    /**
     * Get application path
     */
    public function getApplicationPath(): string;

    /**
     * Get local data path
     */
    public function getLocalDataPath(): string;

    /**
     * Get shared data path
     */
    public function getSharedDataPath(): string;

    /**
     * Get application name
     */
    public function getApplicationName(): string;

    /**
     * Register custom loaders into the stack
     */
    public function initializeLoaders(StackLoader $loader): void;

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

    /**
     * Get build manifest
     */
    public function getBuildManifest(): ?BuildManifest;
}
