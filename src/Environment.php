<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

use DecodeLabs\Genesis\Environment\Config as EnvConfig;
use DecodeLabs\Genesis\Environment\Mode;

class Environment
{
    protected string $name = 'default';
    protected Mode $mode = Mode::Production;

    /**
     * Init with config
     */
    public function __construct(
        EnvConfig $config
    ) {
        // Env name
        $this->name = $config->getEnvironmentName() ?? $this->name;

        // Run mode
        $this->mode = $config->getMode() ?? $this->mode;

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
     */
    public function getMode(): Mode
    {
        return $this->mode;
    }

    /**
     * Is running in development mode
     */
    public function isDevelopment(): bool
    {
        return $this->mode === Mode::Development;
    }

    /**
     * Is running in testing mode (or development)
     */
    public function isTesting(): bool
    {
        return
            $this->mode === Mode::Testing ||
            $this->mode === Mode::Development;
    }

    /**
     * Is running in production mode
     */
    public function isProduction(): bool
    {
        return $this->mode === Mode::Production;
    }
}
