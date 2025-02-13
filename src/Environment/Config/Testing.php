<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Environment\Config;

class Testing extends Production
{
    protected const DefaultName = 'testing';

    public ?bool $displayErrors = true;
    public ?int $errorReporting = E_ALL & ~E_NOTICE;
}
