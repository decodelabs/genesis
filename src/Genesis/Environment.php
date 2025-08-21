<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

use DecodeLabs\Genesis\Environment\Config as EnvConfig;
use DecodeLabs\Monarch\Environment as EnvironmentInterface;
use DecodeLabs\Monarch\EnvironmentMode as Mode;
use Locale;

class Environment implements EnvironmentInterface
{
    public protected(set) string $name = 'default';
    public protected(set) Mode $mode = Mode::Production;

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

        // Locale
        Locale::setDefault($config->defaultLocale ?? 'en_US');

        // Timezone
        date_default_timezone_set($config->defaultTimezone ?? 'UTC');

        // MB encoding
        if (function_exists('mb_internal_encoding')) {
            mb_internal_encoding('UTF-8');
        }
    }
}
