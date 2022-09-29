<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

/**
 * global helpers
 */

namespace DecodeLabs\Genesis
{
    use DecodeLabs\Genesis;
    use DecodeLabs\Veneer;

    // Register the Veneer facade
    Veneer::register(Context::class, Genesis::class);
}
