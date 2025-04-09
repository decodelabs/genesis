<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Build;

use DecodeLabs\Archetype;
use DecodeLabs\Atlas;
use DecodeLabs\Atlas\Dir;
use DecodeLabs\Atlas\File;
use DecodeLabs\Exceptional;
use DecodeLabs\Genesis;
use DecodeLabs\Genesis\Bootstrap\Buildable;
use DecodeLabs\Glitch\Proxy as Glitch;
use DecodeLabs\Monarch;
use Generator;
use Throwable;

class Handler
{
    protected(set) string $buildId;
    public bool $compile = true;
    protected(set) Manifest $manifest;

    /**
     * Init with manifest
     */
    public function __construct(
        Manifest $manifest
    ) {
        $this->manifest = $manifest;
        $this->buildId = $manifest->generateBuildId();
        $this->compile = Genesis::$bootstrap instanceof Buildable;
    }

    /**
     * Get manifest
     */
    public function getManifest(): Manifest
    {
        return $this->manifest;
    }

    /**
     * Get build ID
     */
    public function getBuildId(): string
    {
        return $this->buildId;
    }

    /**
     * Set compile
     */
    public function setCompile(
        bool $compile
    ): void {
        $this->compile = $compile;
    }

    /**
     * Should compile
     */
    public function shouldCompile(): bool
    {
        return $this->compile;
    }



    /**
     * Run full build process
     */
    public function run(): void
    {
        $session = $this->manifest->getCliSession();

        // Prepare info
        $buildId = $this->getBuildId();

        if (!$this->shouldCompile()) {
            $session->info('Builder is running in dev mode, no build folder will be created');
        }

        // Creating build
        $session->inlineInfo('Using build id: ');
        $session->{'.brightMagenta'}($buildId);
        $session->newLine();


        // Run custom tasks
        $this->runPreCompileTasks();


        if ($this->shouldCompile()) {
            // Compile
            $this->compile();

            // Post compile tasks
            $this->runPostCompileTasks();

            // Activate
            $this->activate();
        } else {
            // Post compile tasks
            $this->runPostCompileTasks();
        }

        // Post activation tasks
        $this->runPostActivationTasks();
    }




    /**
     * Run pre compile tasks
     */
    public function runPreCompileTasks(): void
    {
        $this->runTaskList($this->manifest->scanPreCompileTasks());
    }


    /**
     * Run compilation
     */
    public function compile(): Dir
    {
        $bootstrap = $this->getBuildableBootstrap();

        $session = $this->manifest->getCliSession();
        $session->newLine();
        $session->{'.yellow|italic|dim'}('⇒ Packaging files'); // @ignore-non-ascii


        // Get build temp dir
        $destination = $this->manifest->getBuildTempDir()->getDir($this->buildId);

        if ($destination->exists()) {
            throw Exceptional::Runtime(
                message: 'Destination build temp dir already exists',
                data: $destination
            );
        }

        $destination->ensureExists(0777);
        $destinationPath = (string)$destination;



        // Create build
        $merger = function(
            File|Dir $node,
            string $location
        ) use ($session, $destination, $destinationPath) {
            $session->write(' - ');
            $session->{'cyan'}(Glitch::normalizePath((string)$node));

            if (!$node->exists()) {
                $session->{'.brightRed'}(' skipped');
                return;
            }

            $session->{'.white'}(' ' . $location);

            $location = ltrim($location, '/');

            if ($node instanceof Dir) {
                $node->mergeInto($destinationPath . '/' . $location);
            } else {
                $node->copy($destination . '/' . $location);
            }
        };


        // Provider files
        $rootDir = Atlas::dir(Monarch::$paths->root);

        foreach($this->scanProviders() as $provider) {
            $session->newLine();
            $session->{'.brightMagenta|bold'}($provider->name);

            foreach ($provider->scanBuildItems($rootDir) as $node => $location) {
                $merger($node, $location);
            }
        }

        // Manifest packages
        foreach ($this->manifest->scanPackages() as $package) {
            $session->newLine();
            $session->{'.brightMagenta|bold'}($package->name);

            foreach ($this->manifest->scanPackage($package) as $node => $location) {
                $merger($node, $location);
            }
        }




        // Create entry
        $entryName = $bootstrap->buildEntry;
        $file = $destination->getFile($entryName . '.disabled');

        $session->write(' - ');
        $session->{'cyan'}($entryName);
        $session->{'.white'}(' ' . $entryName);

        $this->manifest->writeEntryFile($file, $this->buildId);

        $session->newLine();
        $session->newLine();

        return $destination;
    }



    /**
     * Run build activation
     */
    public function activate(): void
    {
        $session = $this->manifest->getCliSession();
        $session->newLine();
        $session->{'.yellow|italic|dim'}('⇒ Activating new build'); // @ignore-non-ascii

        // Get source dir
        $source = $this->manifest->getBuildTempDir()->getDir($this->buildId);

        if (!$source->exists()) {
            throw Exceptional::Runtime(
                message: 'Build has not been compiled and cannot be activated'
            );
        }

        // Activate build
        $strategy = $this->loadStrategy();
        $strategy->activate($source, $session);

        // Clear caches
        clearstatcache(true);

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }


        // Clear up dirs
        $this->manifest->getBuildTempDir()->delete();

        $session->newLine();
    }


    /**
     * Run post compile tasks
     */
    public function runPostCompileTasks(): void
    {
        $this->runTaskList($this->manifest->scanPostCompileTasks());
    }


    /**
     * Run post activation tasks
     */
    public function runPostActivationTasks(): void
    {
        $this->runTaskList($this->manifest->scanPostActivationTasks());
    }

    /**
     * Run task list
     *
     * @param Generator<Task> $tasks
     */
    protected function runTaskList(
        Generator $tasks
    ): void {
        $session = $this->manifest->getCliSession();
        $session->newLine();

        $tasks = iterator_to_array($tasks);
        uasort($tasks, fn($a, $b) => $b->priority <=> $a->priority);

        foreach ($tasks as $task) {
            $session->{'.yellow|italic|dim'}('⇒ ' . $task->description); // @ignore-non-ascii

            try {
                $task->run($session);
            } catch (Throwable $e) {
                Glitch::logException($e);
                $session->error($e->getMessage());
            }

            $session->newLine();
        }
    }




    /**
     * Clear builds
     */
    public function clear(): void
    {
        $session = $this->manifest->getCliSession();
        $session->newLine();
        $session->{'.yellow|italic|dim'}('⇒ Clearing builds'); // @ignore-non-ascii

        $this->loadStrategy()->clear($session);
    }


    protected function getBuildableBootstrap(): Buildable
    {
        if (!Genesis::$bootstrap instanceof Buildable) {
            throw Exceptional::Runtime(
                message: 'Build handler can only be used with buildable bootstrap'
            );
        }

        return Genesis::$bootstrap;
    }

    protected function loadStrategy(): Strategy
    {
        $bootstrap = $this->getBuildableBootstrap();

        $class = Archetype::resolve(
            Strategy::class,
            $bootstrap->buildStrategy
        );

        return new $class(
            $bootstrap,
            $this->manifest
        );
    }

    /**
     * @return Generator<Provider>
     */
    protected function scanProviders(): Generator
    {
        foreach(Archetype::scanClasses(
            Provider::class
        ) as $class) {
            yield new $class();
        }
    }
}
