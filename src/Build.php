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
    public ?int $time = null;
    public bool $compiled = false;
    public string $path;

    protected int $cacheBuster;
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
     * Get build time
     */
    public function getTime(): ?int
    {
        return $this->time;
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

    /**
     * Get cache buster
     */
    public function getCacheBuster(): int
    {
        if (!isset($this->cacheBuster)) {
            $this->cacheBuster = $this->time ?? time();
        }

        return $this->cacheBuster;
    }

    /**
     * Get path
     */
    public function getPath(): string
    {
        return $this->path;
    }


    /**
     * Is compiled
     */
    public function isCompiled(): bool
    {
        return $this->compiled;
    }

    /**
     * Is running from source
     */
    public function isSource(): bool
    {
        return !$this->compiled;
    }


    /**
     * Get Handler
     */
    public function getHandler(): Handler
    {
        static $handler;

        if (!isset($handler)) {
            if (null === ($manifest = $this->context->hub->getBuildManifest())) {
                throw Exceptional::Setup('Hub does not provide a build manifest');
            }

            $handler = new Handler($manifest);
        }

        return $handler;
    }
}
