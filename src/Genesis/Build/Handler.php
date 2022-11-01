<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Build;

use DecodeLabs\Atlas\Dir;
use DecodeLabs\Atlas\File;
use DecodeLabs\Exceptional;
use DecodeLabs\Genesis;
use DecodeLabs\Glitch\Proxy as Glitch;
use Generator;
use Throwable;

class Handler
{
    protected string $buildId;
    protected bool $compile = true;
    protected Manifest $manifest;

    /**
     * Init with manifest
     */
    public function __construct(Manifest $manifest)
    {
        $this->manifest = $manifest;
        $this->buildId = $manifest->generateBuildId();
        $this->compile = !Genesis::$environment->isDevelopment();
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
    public function setCompile(bool $compile): void
    {
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
        $session = $this->manifest->getCliSession();
        $session->newLine();
        $session->{'.yellow|italic|dim'}('⇒ Packaging files'); // @ignore-non-ascii


        // Get build temp dir
        $destination = $this->manifest->getBuildTempDir()->getDir($this->buildId);

        if ($destination->exists()) {
            throw Exceptional::Runtime(
                'Destination build temp dir already exists',
                null,
                $destination
            );
        }

        $destination->ensureExists(0777);
        $destinationPath = (string)$destination;



        // Create build
        foreach ($this->manifest->scanPackages() as $package) {
            $session->newLine();
            $session->{'.brightMagenta|bold'}($package->getName());

            foreach ($this->manifest->scanPackage($package) as $node => $location) {
                $session->write(' - ');
                $session->{'cyan'}(Glitch::normalizePath((string)$node));

                if (!$node->exists()) {
                    $session->{'.brightRed'}(' skipped');
                    continue;
                }

                $session->{'.white'}(' ' . $location);

                $location = ltrim($location, '/');

                if ($node instanceof Dir) {
                    $node->mergeInto($destinationPath . '/' . $location);
                } else {
                    $node->copy($destination . '/' . $location);
                }
            }
        }


        // Create entry
        $entryName = $this->manifest->getEntryFileName();
        $file = $destination->getFile($entryName . '.disabled');

        $session->write(' - ');
        $session->{'cyan'}($entryName);
        $session->{'.white'}(' ' . $entryName);

        $this->manifest->writeEntryFile($file);

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
            throw Exceptional::Runtime('Build has not been compiled and cannot be activated');
        }


        // Prepare
        $rootDir = $this->manifest->getRunDir();
        $entryName = $this->manifest->getEntryFileName();

        $runName1 = $this->manifest->getRunName1();
        $runDir1 = $rootDir->getDir($runName1);
        $runFile1 = $runDir1->getFile($entryName);

        $runName2 = $this->manifest->getRunName2();
        $runDir2 = $rootDir->getDir($runName2);
        $runFile2 = $runDir2->getFile($entryName);

        clearstatcache(true);



        // Check for existing
        $active1Exists = $runFile1->exists();
        $active2Exists = $runFile2->exists();

        if ($active1Exists && $active2Exists) {
            $runFile2->renameTo('Run.php.disabled');
            $active2Exists = false;
            clearstatcache(true);
        }

        if ($active1Exists) {
            $current = $runDir1;
            $old = $runDir2;
        } elseif ($active2Exists) {
            $current = $runDir2;
            $old = $runDir1;
        } else {
            $current = null;
            $old = $runDir1;
        }

        $targetName = $old->getName();
        $session->{'.cyan'}($old->getPath());

        // Move previous out the way
        if ($old->exists()) {
            $session->write(' - ');
            $session->{'yellow'}($old->getName());
            $session->write(' > ');
            $session->{'.red'}('deleted');

            $old->delete();
        }


        // Move source to runDir
        $session->write(' - ');
        $session->{'yellow'}($this->buildId);
        $session->write(' > ');
        $session->{'.green'}($targetName);

        $source->moveTo((string)$rootDir, $targetName);
        sleep(1);


        // Enable entry file
        $session->write(' - ');
        $session->{'white|dim'}($targetName . '/' . $entryName . '.disabled');
        $session->write(' > ');
        $session->{'.green'}($targetName . '/' . $entryName);

        $source->getFile($entryName . '.disabled')->renameTo($entryName);


        // Disable active entry file
        if ($current !== null) {
            $session->write(' - ');
            $session->{'yellow'}($current->getName() . '/' . $entryName);
            $session->write(' > ');
            $session->{'.white|dim'}($current->getName() . '/' . $entryName . '.disabled');

            $current->getFile($entryName)->renameTo($entryName . '.disabled');
        }


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
    protected function runTaskList(Generator $tasks): void
    {
        $session = $this->manifest->getCliSession();
        $session->newLine();

        foreach ($tasks as $task) {
            $session->{'.yellow|italic|dim'}('⇒ ' . $task->getDescription()); // @ignore-non-ascii

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

        $rootDir = $this->manifest->getRunDir();
        $entryName = $this->manifest->getEntryFileName();

        $runName1 = $this->manifest->getRunName1();
        $runDir1 = $rootDir->getDir($runName1);
        $runFile1 = $runDir1->getFile($entryName);

        $runName2 = $this->manifest->getRunName2();
        $runDir2 = $rootDir->getDir($runName2);
        $runFile2 = $runDir2->getFile($entryName);
        $found = false;


        if ($runFile1->existS()) {
            $runFile1->delete();
            $session->deleteSuccess((string)$runFile1);
            $found = true;
        }

        if ($runFile2->existS()) {
            $runFile2->delete();
            $session->deleteSuccess((string)$runFile2);
            $found = true;
        }

        if ($runDir1->existS()) {
            $runDir1->delete();
            $session->deleteSuccess((string)$runDir1);
            $found = true;
        }

        if ($runDir2->existS()) {
            $runDir2->delete();
            $session->deleteSuccess((string)$runDir2);
            $found = true;
        }

        if ($rootDir->existS()) {
            $rootDir->delete();
            $session->deleteSuccess((string)$rootDir);
            $found = true;
        }

        if (!$found) {
            $session->info('No builds found');
        }

        $session->newLine();
    }
}
