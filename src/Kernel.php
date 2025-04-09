<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

interface Kernel
{
    public string $mode { get; }

    public function initialize(): void;
    public function run(): void;
    public function shutdown(): never;
}
