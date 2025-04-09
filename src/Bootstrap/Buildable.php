<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Bootstrap;

require_once dirname(__DIR__).'/Bootstrap.php';

use DecodeLabs\Genesis\Bootstrap;

interface Buildable extends Bootstrap
{
    public string $runDir { get; }
    public string $buildEntry { get; }
    public string $buildStrategy { get; }
}
