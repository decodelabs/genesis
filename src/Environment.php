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
    protected(set) string $name = 'default';
    protected(set) Mode $mode = Mode::Production;

    /**
     * Init with config
     */
    public function __construct(
        EnvConfig $config
    ) {
        // Env name
        $this->name = $config->name ?? $this->name;

        // Run mode
        $this->mode = $config->mode ?? $this->mode;

        // Umask
        if (null !== ($umask = $config->umask)) {
            umask($umask);
        }

        // Error reporting
        if (null !== ($errorReporting = $config->errorReporting)) {
            error_reporting($errorReporting);
        }

        // Display errors
        if (null !== ($displayErrors = $config->displayErrors)) {
            ini_set('display_errors', (string)$displayErrors);
        }

        // Timezone
        date_default_timezone_set($config->defaultTimezone ?? 'UTC');

        // MB encoding
        if (function_exists('mb_internal_encoding')) {
            mb_internal_encoding('UTF-8');
        }
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
