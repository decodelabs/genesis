<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

interface Loader
{
    public function getPriority(): int;

    public function loadClass(
        string $class
    ): void;
}
