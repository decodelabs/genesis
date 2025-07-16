<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Environment\Config;

use DecodeLabs\Genesis\Environment\Config;
use DecodeLabs\Monarch\EnvironmentMode as Mode;

class Production implements Config
{
    protected const DefaultName = 'production';

    public protected(set) ?string $name {
        get => $this->name ?? $this->getDefaultName();
    }

    public Mode $mode {
        get => Mode::fromName($this->getDefaultName());
    }

    public ?int $umask = null;
    public ?bool $displayErrors = null;
    public protected(set) ?int $errorReporting = E_ALL & ~E_NOTICE & ~E_DEPRECATED;
    public protected(set) ?string $defaultTimezone = 'UTC';

    public function __construct(
        ?string $name = null
    ) {
        $this->name = $name;
    }

    protected function getDefaultName(): string
    {
        /** @var string $output */
        $output = static::DefaultName;
        return $output;
    }
}
