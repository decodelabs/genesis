<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs;

use DecodeLabs\Genesis\AnalysisMode;
use DecodeLabs\Genesis\Build;
use DecodeLabs\Genesis\Build\Handler as BuildHandler;
use DecodeLabs\Genesis\Environment;
use DecodeLabs\Genesis\Environment\Config\Development as DevelopmentConfig;
use DecodeLabs\Genesis\Hub;
use DecodeLabs\Kingdom\Service;
use DecodeLabs\Kingdom\ServiceTrait;

class Genesis implements Service
{
    use ServiceTrait;

    public protected(set) Hub $hub;
    public protected(set) Environment $environment;
    public protected(set) Build $build;

    public protected(set) BuildHandler $buildHandler {
        get {
            if (!isset($this->buildHandler)) {
                if (null === ($manifest = $this->hub->buildManifest)) {
                    throw Exceptional::Setup(
                        message: 'Hub does not provide a build manifest'
                    );
                }

                $this->buildHandler = new Slingshot(
                    types: [
                        $manifest,
                    ]
                )->resolveInstance(BuildHandler::class);
            }

            return $this->buildHandler;
        }
    }

    public protected(set) Kingdom $kingdom;

    public function __construct(
        string $rootPath,
        string $hubClass,
        ?AnalysisMode $analysisMode = null
    ) {
        // Default environment
        $this->environment = new Environment(
            new DevelopmentConfig()
        );

        // Start time
        Monarch::setStartTime(microtime(true));

        // Bootstrap
        $paths = Monarch::getPaths();
        $paths->root = $rootPath;

        // Load hub
        $class = new Archetype()->resolve(Hub::class, $hubClass);
        $this->hub = new $class($this, $analysisMode);

        // Build info
        $this->build = $this->hub->loadBuild();
        Monarch::setBuild($this->build);
        $paths->run = $this->build->path;

        // Loaders
        $this->hub->initializeLoaders();

        // Init environment
        $this->environment = new Environment($this->hub->loadEnvironmentConfig());
        Monarch::setEnvironment($this->environment);

        // Platform
        $this->hub->initializePlatform();

        // Load kingdom
        $this->kingdom = $this->hub->loadKingdom();
        $this->kingdom->container->set(self::class, $this);
        Monarch::setKingdom($this->kingdom);
        $this->kingdom->initialize();
    }

    public function run(): void
    {
        $this->kingdom->run();
        $this->kingdom->shutdown();
    }
}
