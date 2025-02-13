<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Build\Task;

use Closure;
use DecodeLabs\Genesis\Build\Task;
use DecodeLabs\Terminus\Session;

class Generic implements Task
{
    protected(set) string $description;
    protected Closure $callback;

    /**
     * Init with callable
     *
     * @param callable(Session $session): void $callback
     */
    public function __construct(
        string $description,
        callable $callback
    ) {
        $this->description = $description;
        $this->callback = Closure::fromCallable($callback);
    }

    /**
     * Run task
     */
    public function run(
        Session $session
    ): void {
        $this->callback->__invoke($session);
    }
}
