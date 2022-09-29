<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Environment\Config;

use DecodeLabs\Genesis\Environment\Config;

class Production implements Config
{
    public const DEFAULT_NAME = 'production';

    protected ?string $name = null;

    public function __construct(?string $name = null)
    {
        $this->name = $name;
    }

    public function getEnvironmentName(): ?string
    {
        return $this->name ?? static::DEFAULT_NAME;
    }

    public function getRunMode(): ?string
    {
        return static::DEFAULT_NAME;
    }

    public function getUmask(): ?int
    {
        return null;
    }

    public function getErrorReporting(): ?int
    {
        return E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED;
    }

    public function getDisplayErrors(): ?bool
    {
        return false;
    }

    public function getDefaultTimezone(): ?string
    {
        return 'UTC';
    }
}
