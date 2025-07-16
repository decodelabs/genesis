<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

use DecodeLabs\Archetype;
use DecodeLabs\Exceptional;
use DecodeLabs\Genesis;
use DecodeLabs\Genesis\Environment\Config\Development as DevelopmentConfig;
use DecodeLabs\Genesis\Loader\Stack as StackLoader;
use DecodeLabs\Monarch;
use DecodeLabs\Pandora\Container;
use DecodeLabs\Veneer;
use DecodeLabs\Veneer\Plugin;

class Context
{
    #[Plugin]
    public Bootstrap $bootstrap;

    #[Plugin]
    public StackLoader $loader;

    #[Plugin]
    public Hub $hub;

    #[Plugin]
    public Build $build;

    #[Plugin]
    public Environment $environment;

    #[Plugin]
    public Kernel $kernel;

    protected float $startTime;

    /**
     * Init with optional Container
     */
    public function __construct()
    {
        // Get a Pandora container in ASAP if available
        if (class_exists(Container::class)) {
            Monarch::replaceContainer(new Container());
        }

        $this->loader = new StackLoader();

        $this->environment = new Environment(
            new DevelopmentConfig()
        );
    }


    /**
     * Bootstrap application
     */
    public function bootstrap(
        Bootstrap $bootstrap
    ): Kernel {
        if (isset($this->startTime)) {
            throw Exceptional::Setup(
                message: 'Context has already been initialized'
            );
        }

        // Start time
        $this->startTime = microtime(true);

        // Bootstrap
        $this->bootstrap = $bootstrap;
        Monarch::$paths->root = Genesis::$bootstrap->rootPath;

        // Load hub
        $class = Archetype::resolve(Hub::class, $bootstrap->hubClass);
        $this->hub = new $class($this, $bootstrap);

        // Build info
        $this->build = $this->hub->loadBuild();
        Monarch::$paths->run = $this->build->path;

        // Loaders
        $this->hub->initializeLoaders($this->loader);

        // Init environment
        $this->environment = new Environment($this->hub->loadEnvironmentConfig());
        Monarch::setEnvironmentMode($this->environment->mode);

        // Platform
        $this->hub->initializePlatform();

        // Kernel
        $this->kernel = $this->hub->loadKernel();
        $this->kernel->initialize();

        return $this->kernel;
    }

    /**
     * Bootstrap application
     */
    public function bootstrapAndRun(
        Bootstrap $bootstrap
    ): void {
        $kernel = $this->bootstrap($bootstrap);
        $kernel->run();
        $kernel->shutdown();
    }


    /**
     * Get start time
     */
    public function getStartTime(): float
    {
        if (!isset($this->startTime)) {
            throw Exceptional::Setup(
                message: 'Genesis has not been run yet'
            );
        }

        return $this->startTime;
    }
}


// Register the Veneer facade
Veneer\Manager::getGlobalManager()->register(
    Context::class,
    Genesis::class
);
