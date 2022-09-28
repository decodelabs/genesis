<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

interface Kernel
{
    public function __construct(Context $context);

    public function run(): void;
    public function shutdown(): void;
}
