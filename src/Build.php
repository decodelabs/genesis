<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

use DecodeLabs\Exceptional;
use DecodeLabs\Genesis\Build\Handler;

class Build
{
    protected(set) ?int $time = null;
    protected(set) bool $compiled = false;
    protected(set) string $path;

    protected(set) int $cacheBuster {
        get {
            if (!isset($this->cacheBuster)) {
                $this->cacheBuster = $this->time ?? time();
            }

            return $this->cacheBuster;
        }
    }

    protected(set) Handler $handler {
        get {
            if(!isset($this->handler)) {
                if (null === ($manifest = $this->context->hub->buildManifest)) {
                    throw Exceptional::Setup(
                        message: 'Hub does not provide a build manifest'
                    );
                }

                $this->handler = new Handler($manifest);
            }

            return $this->handler;
        }
    }

    protected Context $context;

    /**
     * @param ?int $time Time the active app was built - if running from source, pass null
     */
    public function __construct(
        Context $context,
        string $path,
        ?int $time = null
    ) {
        $this->context = $context;
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
            $this->context->environment->isDevelopment();
    }
}
