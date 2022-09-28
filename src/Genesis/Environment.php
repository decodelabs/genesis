<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

use DecodeLabs\Genesis\Environment\Config as EnvConfig;

class Environment
{
    protected string $name = 'default';

    /**
     * @phpstan-var value-of<EnvConfig::RUN_MODES>
     */
    protected string $runMode = 'production';

    /**
     * Init with config
     */
    public function __construct(EnvConfig $config)
    {
        // Env name
        $this->name = $config->getEnvironmentName() ?? $this->name;

        // Run mode
        $this->runMode = $config->getRunMode() ?? $this->runMode;

        // Umask
        if (null !== ($umask = $config->getUmask())) {
            umask($umask);
        }

        // Error reporting
        if (null !== ($errorReporting = $config->getErrorReporting())) {
            error_reporting($errorReporting);
        }

        // Display errors
        if (null !== ($displayErrors = $config->getDisplayErrors())) {
            ini_set('display_errors', (string)$displayErrors);
        }

        // Timezone
        date_default_timezone_set($config->getDefaultTimezone() ?? 'UTC');

        // MB encoding
        if (function_exists('mb_internal_encoding')) {
            mb_internal_encoding('UTF-8');
        }
    }

    /**
     * Get environment name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get run mode
     *
     * @phpstan-return value-of<EnvConfig::RUN_MODES>
     */
    public function getRunMode(): string
    {
        return $this->runMode;
    }

    /**
     * Is running in development mode
     */
    public function isDevelopment(): bool
    {
        return $this->runMode === 'development';
    }

    /**
     * Is running in testing mode
     */
    public function isTesting(): bool
    {
        return $this->runMode === 'testing';
    }

    /**
     * Is running in production mode
     */
    public function isProduction(): bool
    {
        return $this->runMode === 'production';
    }
}
