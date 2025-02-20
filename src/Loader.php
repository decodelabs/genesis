<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

interface Loader
{
    public int $priority { get; }

    public function loadClass(
        string $class
    ): void;
}
