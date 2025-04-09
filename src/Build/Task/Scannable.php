<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Build\Task;

use DecodeLabs\Genesis\Build\Task;
use DecodeLabs\Terminus\Session;

interface Scannable extends Task
{
    public function __construct();
}
