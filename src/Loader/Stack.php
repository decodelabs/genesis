<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Loader;

use DecodeLabs\Genesis\Loader;

class Stack implements Loader
{
    /**
     * @var array<string, Loader>
     */
    protected array $loaders = [];

    /**
     * Top priority
     */
    public function getPriority(): int
    {
        return 0;
    }

    public function registerLoader(
        Loader $loader
    ): void {
        if (empty($this->loaders)) {
            spl_autoload_register([$this, 'loadClass'], true, true);
        }

        $this->loaders[get_class($loader)] = $loader;

        uasort($this->loaders, function (Loader $a, Loader $b) {
            return $a->getPriority() <=> $b->getPriority();
        });
    }

    public function unregisterLoader(
        Loader $loader
    ): void {
        unset($this->loaders[get_class($loader)]);

        if (empty($this->loaders)) {
            spl_autoload_unregister([$this, 'loadClass']);
        }
    }

    /**
     * Cycle through loaders until class is found
     */
    public function loadClass(
        string $class
    ): void {
        foreach ($this->loaders as $loader) {
            $loader->loadClass($class);

            if (class_exists($class, false)) {
                return;
            }
        }
    }
}
