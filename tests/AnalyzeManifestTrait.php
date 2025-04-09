<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Tests;

use DecodeLabs\Atlas\File;
use DecodeLabs\Genesis\Build\Manifest;
use DecodeLabs\Genesis\Build\ManifestTrait;

class AnalyzeManifestTrait implements Manifest
{
    use ManifestTrait;

    public function writeEntryFile(
        File $file,
        string $buildId
    ): void {
        // No-op
    }
};
