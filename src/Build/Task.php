<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Build;

use DecodeLabs\Terminus\Session;

interface Task
{
    public function getDescription(): string;
    public function run(Session $session): void;
}
