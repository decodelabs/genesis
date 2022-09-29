<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

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
     * Register custom loaders into the stack
     */
    public function initializeLoaders(StackLoader $loader): void;

    /**
     * Load object to convey environment configuration
     */
    public function loadEnvironmentConfig(): EnvConfig;

    /**
     * Setup error handler system
     */
    public function initializeErrorHandler(): void;

    /**
     * Load kernel to handle running the app
     */
    public function loadKernel(): Kernel;
}
