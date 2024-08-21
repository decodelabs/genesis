<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Environment;

interface Config
{
    public function getEnvironmentName(): ?string;
    public function getMode(): ?Mode;

    public function getUmask(): ?int;
    public function getErrorReporting(): ?int;
    public function getDisplayErrors(): ?bool;

    public function getDefaultTimezone(): ?string;
}
