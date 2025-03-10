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
use DecodeLabs\Pandora\Container;
use DecodeLabs\Veneer;
use DecodeLabs\Veneer\Plugin;

class Context
{
    #[Plugin]
    public Container $container;

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
    public function __construct(
        ?Container $container = null
    ) {
        $this->replaceContainer($container ?? new Container());
        $this->loader = new StackLoader();

        $this->environment = new Environment(
            new DevelopmentConfig()
        );
    }


    /**
     * Replace container for whole application
     */
    public function replaceContainer(
        Container $container
    ): void {
        $this->container = $container;
        $container->bindShared(Context::class, $this);
        Veneer::setContainer($this->container);
    }


    /**
     * @param array<string,mixed> $options
     */
    final public function run(
        string $hubName,
        array $options = []
    ): void {
        $kernel = $this->initialize($hubName, $options);
        $kernel->run();
        $kernel->shutdown();
    }

    /**
     * @param array<string,mixed> $options
     */
    public function initialize(
        string $hubName,
        array $options = []
    ): Kernel {
        if (isset($this->startTime)) {
            throw Exceptional::Setup(
                message: 'Context has already been initialized'
            );
        }

        $this->startTime = microtime(true);

        // Load hub
        $class = Archetype::resolve(Hub::class, $hubName);
        $this->hub = new $class($this, $options);

        // Build info
        $this->build = $this->hub->loadBuild();

        // Loaders
        $this->hub->initializeLoaders($this->loader);

        // Init environment
        $this->environment = new Environment($this->hub->loadEnvironmentConfig());

        // Platform
        $this->hub->initializePlatform();

        // Kernel
        $this->kernel = $this->hub->loadKernel();
        $this->kernel->initialize();

        return $this->kernel;
    }


    /**
     * Execute application
     */
    public function execute(): void
    {
        throw Exceptional::Deprecated(
            message: 'Context::execute() has been deprecated in favour of Context::run()'
        );
    }


    /**
     * Shutdown system
     */
    public function shutdown(): void
    {
        $this->kernel->shutdown();
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
