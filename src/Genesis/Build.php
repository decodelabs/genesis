<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

use DecodeLabs\Genesis;
use DecodeLabs\Monarch\Build as BuildInterface;
use DecodeLabs\Monarch\EnvironmentMode;

class Build implements BuildInterface
{
    public protected(set) ?int $time = null;
    public protected(set) bool $compiled = false;
    public protected(set) string $path;

    public protected(set) int $cacheBuster {
        get {
            if (!isset($this->cacheBuster)) {
                $this->cacheBuster = $this->time ?? time();
            }

            return $this->cacheBuster;
        }
    }

    protected Genesis $genesis;

    /**
     * @param ?int $time Time the active app was built - if running from source, pass null
     */
    public function __construct(
        Genesis $genesis,
        string $path,
        ?int $time = null
    ) {
        $this->genesis = $genesis;
        $this->path = $path;
        $this->time = $time;
        $this->compiled = $time !== null;
    }

    /**
     * Should cache bust
     */
    public function shouldCacheBust(): bool
    {
        return
            $this->compiled ||
            $this->genesis->environment->mode === EnvironmentMode::Development;
    }
}
