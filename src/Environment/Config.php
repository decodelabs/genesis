<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Environment;

interface Config
{
    public ?string $name { get; }
    public ?Mode $mode { get; }
    public ?bool $displayErrors { get; set; }
    public ?int $errorReporting { get; }
    public ?int $umask { get; set; }
    public ?string $defaultTimezone { get; }
}
