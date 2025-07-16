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
use DecodeLabs\Genesis\Build\Task\PostActivation;
use DecodeLabs\Genesis\Build\Task\PostCompile;
use DecodeLabs\Genesis\Build\Task\PreCompile;
use DecodeLabs\Genesis\Build\Task\Scannable;
use DecodeLabs\Monarch;
use DecodeLabs\Terminus\Session;
use Generator;

trait ManifestTrait
{
    public function getCliSession(): Session
    {
        return Session::getDefault();
    }

    public function generateBuildId(): string
    {
        return uniqid('', true);
    }

    public function getBuildTempDir(): Dir
    {
        return Atlas::dir(Monarch::$paths->localData . '/build/');
    }


    /**
     * @return Generator<Package>
     */
    public function scanPackages(): Generator
    {
        yield from [];
    }

    /**
     * @return Generator<File|Dir,string>
     */
    public function scanPackage(
        Package $package
    ): Generator {
        yield from [];
    }


    /**
     * @return Generator<PreCompile>
     */
    public function scanPreCompileTasks(): Generator
    {
        foreach ($this->scanAllTasks() as $class) {
            if (!is_a($class, PreCompile::class, true)) {
                continue;
            }

            yield new $class();
        }
    }

    /**
     * @return Generator<PostCompile>
     */
    public function scanPostCompileTasks(): Generator
    {
        foreach ($this->scanAllTasks() as $class) {
            if (!is_a($class, PostCompile::class, true)) {
                continue;
            }

            yield new $class();
        }
    }

    /**
     * @return Generator<PostActivation>
     */
    public function scanPostActivationTasks(): Generator
    {
        foreach ($this->scanAllTasks() as $class) {
            if (!is_a($class, PostActivation::class, true)) {
                continue;
            }

            yield new $class();
        }
    }

    /**
     * @return Generator<class-string<Scannable>>
     */
    protected function scanAllTasks(): Generator
    {
        foreach (Archetype::scanClasses(Task::class) as $class) {
            if (!is_a($class, Scannable::class, true)) {
                continue;
            }

            yield $class;
        }
    }
}
