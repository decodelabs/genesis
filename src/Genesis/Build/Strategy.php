<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Build;

use DecodeLabs\Atlas\Dir;
use DecodeLabs\Genesis\Bootstrap;
use DecodeLabs\Terminus\Session;

interface Strategy
{
    public function __construct(
        Bootstrap $bootstrap
    );

    public function activate(
        Dir $buildDir,
        Session $session
    ): void;

    public function clear(
        Session $session
    ): void;
}
