<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Build;

use DecodeLabs\Atlas\Dir;

class Package
{
    public string $name;
    public Dir $source;
    public ?string $targetPath;

    /**
     * Init with name and source location
     */
    public function __construct(
        string $name,
        Dir $source,
        ?string $targetPath = null
    ) {
        $this->name = $name;
        $this->source = $source;
        $this->targetPath = $targetPath;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSourceLocation(): Dir
    {
        return $this->source;
    }

    public function getTargetPath(): ?string
    {
        return $this->targetPath;
    }
}
