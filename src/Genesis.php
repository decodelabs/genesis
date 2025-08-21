<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs;

use DecodeLabs\Archetype;
use DecodeLabs\Exceptional;
use DecodeLabs\Genesis\Bootstrap;
use DecodeLabs\Genesis\Build;
use DecodeLabs\Genesis\Build\Handler as BuildHandler;
use DecodeLabs\Genesis\Environment;
use DecodeLabs\Genesis\Environment\Config\Development as DevelopmentConfig;
use DecodeLabs\Genesis\Hub;
use DecodeLabs\Monarch;
use DecodeLabs\Kingdom;
use DecodeLabs\Kingdom\Service;
use DecodeLabs\Kingdom\ServiceTrait;
use DecodeLabs\Slingshot;

class Genesis implements Service
{
    use ServiceTrait;

    public protected(set) Bootstrap $bootstrap;
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
                        $this->bootstrap
                    ]
                )->resolveInstance(BuildHandler::class);
            }

            return $this->buildHandler;
        }
    }

    public function __construct()
    {
        $this->environment = new Environment(
            new DevelopmentConfig()
        );
    }


    public function bootstrap(
        Bootstrap $bootstrap
    ): Kingdom {
        if (isset($this->bootstrap)) {
            throw Exceptional::Setup(
                message: 'Context has already been initialized'
            );
        }

        // Start time
        Monarch::setStartTime(microtime(true));

        // Bootstrap
        $this->bootstrap = $bootstrap;
        $paths = Monarch::getPaths();
        $paths->root = $this->bootstrap->rootPath;

        // Load hub
        $class = new Archetype()->resolve(Hub::class, $bootstrap->hubClass);
        $this->hub = new $class($this, $bootstrap);

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

        // Kingdom
        $kingdom = $this->hub->loadKingdom();
        $kingdom->container->set(self::class, $this);
        Monarch::setKingdom($kingdom);
        $kingdom->initialize();

        return $kingdom;
    }

    public function bootstrapAndRun(
        Bootstrap $bootstrap
    ): void {
        $kingdom = $this->bootstrap($bootstrap);
        $kingdom->run();
        $kingdom->shutdown();
    }
}
