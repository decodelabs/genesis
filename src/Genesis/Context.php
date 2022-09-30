<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

use DecodeLabs\Archetype;
use DecodeLabs\Exceptional;
use DecodeLabs\Genesis\Loader\Stack as StackLoader;
use DecodeLabs\Pandora\Container;

use DecodeLabs\Veneer;
use DecodeLabs\Veneer\LazyLoad;
use DecodeLabs\Veneer\Plugin;

#[LazyLoad]
class Context
{
    #[Plugin]
    public Container $container;

    #[Plugin]
    public StackLoader $loader;

    #[Plugin]
    #[LazyLoad]
    public Hub $hub;

    #[Plugin]
    #[LazyLoad]
    public Build $build;

    #[Plugin]
    #[LazyLoad]
    public Environment $environment;

    #[Plugin]
    #[LazyLoad]
    public Kernel $kernel;

    protected float $startTime;

    /**
     * Init with optional Container
     */
    public function __construct(Container $container = null)
    {
        $this->replaceContainer($container ?? new Container());
        $this->loader = new StackLoader();
    }


    /**
     * Replace container for whole application
     */
    public function replaceContainer(Container $container): void
    {
        $this->container = $container;
        Veneer::setContainer($this->container);
    }


    /**
     * @param array<string, mixed> $options
     */
    final public function run(
        string $hubName,
        array $options = []
    ): void {
        $this->initialize($hubName, $options);
        $this->execute();
        $this->shutdown();
    }

    /**
     * @param array<string, mixed> $options
     */
    public function initialize(
        string $hubName,
        array $options = []
    ): void {
        if (isset($this->startTime)) {
            throw Exceptional::Setup('Context has already been initialized');
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

        // Error handler
        $this->hub->initializeErrorHandler();

        // Kernel
        $this->kernel = $this->hub->loadKernel();
    }


    /**
     * Execute application
     */
    public function execute(): void
    {
        $this->kernel->run();
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
            throw Exceptional::Setup('Genesis has not been run yet');
        }

        return $this->startTime;
    }
}
