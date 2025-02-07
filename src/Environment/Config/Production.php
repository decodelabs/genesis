<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Environment\Config;

use DecodeLabs\Genesis\Environment\Config;
use DecodeLabs\Genesis\Environment\Mode;

class Production implements Config
{
    protected const DefaultName = 'production';

    protected ?string $name = null;
    protected ?int $umask = null;
    protected ?bool $displayErrors = null;

    public function __construct(
        ?string $name = null
    ) {
        $this->name = $name;
    }

    public function getEnvironmentName(): ?string
    {
        return $this->name ?? static::DefaultName;
    }

    public function getMode(): ?Mode
    {
        return Mode::fromName(static::DefaultName);
    }

    public function setUmask(
        ?int $umask
    ): void {
        $this->umask = $umask;
    }

    public function getUmask(): ?int
    {
        return $this->umask;
    }

    public function getErrorReporting(): ?int
    {
        return E_ALL & ~E_NOTICE & ~E_DEPRECATED;
    }

    public function setDisplayErrors(
        ?bool $errors
    ): void {
        $this->displayErrors = $errors;
    }

    public function getDisplayErrors(): ?bool
    {
        return $this->displayErrors;
    }

    public function getDefaultTimezone(): ?string
    {
        return 'UTC';
    }
}
