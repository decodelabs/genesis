<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

use DecodeLabs\Genesis;
use DecodeLabs\Genesis\Build\Manifest as BuildManifest;
use DecodeLabs\Genesis\Environment\Config as EnvConfig;
use DecodeLabs\Kingdom;

interface Hub
{
    public ?BuildManifest $buildManifest { get; }

    public function __construct(
        Genesis $service,
        ?AnalysisMode $analysisMode = null
    );

    public function initializeLoaders(): void;

    public function loadBuild(): Build;

    public function loadEnvironmentConfig(): EnvConfig;

    public function initializePlatform(): void;

    public function loadKingdom(): Kingdom;
}
