<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Environment\Config;

class Development extends Testing
{
    public const DefaultName = 'development';

    protected ?int $umask = 0;

    public function getErrorReporting(): ?int
    {
        return E_ALL;
    }
}
