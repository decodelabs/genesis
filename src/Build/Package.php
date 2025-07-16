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
    public protected(set) string $name;
    public protected(set) Dir $source;
    public protected(set) ?string $targetPath;

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
}
