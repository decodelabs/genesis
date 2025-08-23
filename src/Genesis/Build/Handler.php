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
use DecodeLabs\Monarch;
use Generator;
use Throwable;

class Handler
{
    public protected(set) string $buildId;
    public bool $compile = true;

    public function __construct(
        protected(set) Manifest $manifest,
        protected Archetype $archetype
    ) {
        $this->buildId = $manifest->generateBuildId();
    }


    public function run(): void
    {
        $session = $this->manifest->getCliSession();

        // Prepare info
        $buildId = $this->buildId;

        if (!$this->compile) {
            $session->info('Builder is running in dev mode, no build folder will be created');
        }

        // Creating build
        $session->inlineInfo('Using build id: ');
        $session->{'.brightMagenta'}($buildId);
        $session->newLine();


        // Run custom tasks
        $this->runPreCompileTasks();


        if ($this->compile) {
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




    public function runPreCompileTasks(): void
    {
        $this->runTaskList($this->manifest->scanPreCompileTasks());
    }


    public function compile(): Dir
    {
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
        $merger = function (
            File|Dir $node,
            string $location
        ) use ($session, $destination, $destinationPath) {
            $session->write(' - ');
            $session->{'cyan'}(Monarch::getPaths()->prettify((string)$node));

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
        $rootDir = Atlas::getDir(Monarch::getPaths()->root);

        foreach ($this->scanProviders() as $provider) {
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
        $entryName = $this->manifest->strategy::BuildEntry;
        $file = $destination->getFile($entryName . '.disabled');

        $session->write(' - ');
        $session->{'cyan'}($entryName);
        $session->{'.white'}(' ' . $entryName);

        $this->manifest->writeEntryFile(
            file: $file,
            buildId: $this->buildId,
            hubClass: get_class(Monarch::getService(Genesis::class)->hub)
        );

        $session->newLine();
        $session->newLine();

        return $destination;
    }



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
        $this->manifest->strategy->activate($source, $session);

        // Clear caches
        clearstatcache(true);

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }


        // Clear up dirs
        $this->manifest->getBuildTempDir()->delete();

        $session->newLine();
    }


    public function runPostCompileTasks(): void
    {
        $this->runTaskList($this->manifest->scanPostCompileTasks());
    }


    public function runPostActivationTasks(): void
    {
        $this->runTaskList($this->manifest->scanPostActivationTasks());
    }

    /**
     * @param Generator<Task> $tasks
     */
    protected function runTaskList(
        Generator $tasks
    ): void {
        $session = $this->manifest->getCliSession();
        $session->newLine();

        $tasks = iterator_to_array($tasks);
        uasort($tasks, fn ($a, $b) => $b->priority <=> $a->priority);

        foreach ($tasks as $task) {
            $session->{'.yellow|italic|dim'}('⇒ ' . $task->description); // @ignore-non-ascii

            try {
                $task->run($session);
            } catch (Throwable $e) {
                Monarch::logException($e);
                $session->error($e->getMessage());
            }

            $session->newLine();
        }
    }




    public function clear(): void
    {
        $session = $this->manifest->getCliSession();
        $session->newLine();
        $session->{'.yellow|italic|dim'}('⇒ Clearing builds'); // @ignore-non-ascii

        $this->manifest->strategy->clear($session);
    }

    /**
     * @return Generator<Provider>
     */
    protected function scanProviders(): Generator
    {
        foreach ($this->archetype->scanClasses(
            Provider::class
        ) as $class) {
            yield new $class();
        }
    }
}
