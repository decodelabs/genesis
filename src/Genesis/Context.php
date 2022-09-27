<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

use DecodeLabs\Pandora\Container;

use DecodeLabs\Veneer\LazyLoad;
use DecodeLabs\Veneer\Plugin;

/**
 * @property Container $container
 */
#[LazyLoad]
class Context
{
    #[Plugin]
    public Container $container;

    /**
     * Init with optional Container
     */
    public function __construct(Container $container = null)
    {
        $this->container = $container ?? new Container();
    }

    /**
     * @param array<string, mixed> $options
     */
    public function run(
        array $options = []
    ): void {
    }
}
