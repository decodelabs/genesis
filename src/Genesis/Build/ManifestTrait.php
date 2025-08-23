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
use DecodeLabs\Slingshot;
use DecodeLabs\Terminus\Session;
use Generator;

trait ManifestTrait
{
    public function __construct(
        protected(set) Strategy $strategy,
        protected Archetype $archetype
    ) {
    }

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
        return Atlas::getDir(Monarch::getPaths()->localData . '/build/');
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
        yield from $this->scanAllTasks(PreCompile::class);
    }

    /**
     * @return Generator<PostCompile>
     */
    public function scanPostCompileTasks(): Generator
    {
        yield from $this->scanAllTasks(PostCompile::class);
    }

    /**
     * @return Generator<PostActivation>
     */
    public function scanPostActivationTasks(): Generator
    {
        yield from $this->scanAllTasks(PostActivation::class);
    }

    /**
     * @template T of Scannable
     * @param class-string<T>|null $type
     * @return ($type is null ? Generator<Scannable> : Generator<T>)
     */
    protected function scanAllTasks(
        ?string $type = null
    ): Generator {
        $slingshot = new Slingshot(archetype: $this->archetype);

        foreach ($this->archetype->scanClasses(Task::class) as $class) {
            if (!is_a($class, Scannable::class, true)) {
                continue;
            }

            if (
                $type !== null &&
                !is_a($class, $type, true)
            ) {
                continue;
            }

            yield $slingshot->resolveInstance($class);
        }
    }
}
