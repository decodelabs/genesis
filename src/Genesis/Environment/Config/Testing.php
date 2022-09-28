<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Environment\Config;

class Testing extends Production
{
    public const DEFAULT_NAME = 'testing';

    public function getErrorReporting(): ?int
    {
        return E_ALL & ~E_NOTICE & ~E_STRICT;
    }

    public function getDisplayErrors(): ?bool
    {
        return true;
    }
}
