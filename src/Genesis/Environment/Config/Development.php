<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Environment\Config;

class Development extends Testing
{
    protected const DefaultName = 'development';

    public ?int $umask = 0;

    public ?int $errorReporting = E_ALL;
}
