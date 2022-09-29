<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Environment;

interface Config
{
    public const RUN_MODES = [
        'development',
        'testing',
        'production'
    ];

    public function getEnvironmentName(): ?string;

    /**
     * @return value-of<self::RUN_MODES>|null
     */
    public function getRunMode(): ?string;

    public function getUmask(): ?int;
    public function getErrorReporting(): ?int;
    public function getDisplayErrors(): ?bool;

    public function getDefaultTimezone(): ?string;
}
