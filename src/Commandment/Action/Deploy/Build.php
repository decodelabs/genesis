<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Commandment\Action\Deploy;

use DecodeLabs\Coercion;
use DecodeLabs\Commandment\Action;
use DecodeLabs\Commandment\Argument;
use DecodeLabs\Commandment\Request;
use DecodeLabs\Genesis;
use DecodeLabs\Kingdom\RuntimeMode;
use DecodeLabs\Monarch;
use DecodeLabs\Systemic;
use DecodeLabs\Terminus\Session;

#[Argument\Flag(
    name: 'force',
    shortcut: 'f',
    description: 'Force compilation'
)]
#[Argument\Flag(
    name: 'dev',
    shortcut: 'd',
    description: 'Build without compilation'
)]
#[Argument\Flag(
    name: 'clear',
    shortcut: 'c',
    description: 'Clear builds'
)]
class Build implements Action
{
    public function __construct(
        protected Session $io,
        protected Genesis $genesis,
        protected Systemic $systemic
    ) {
    }

    public function execute(
        Request $request,
    ): bool {
        $this->ensureCliSource();

        // Setup controller
        if ($request->parameters->asBool('clear')) {
            $this->clear();
        } else {
            $this->run(
                dev: $request->parameters->asBool('dev'),
                force: $request->parameters->asBool('force')
            );
        }

        clearstatcache();

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        return true;
    }

    private function clear(): void
    {
        $handler = $this->genesis->buildHandler;

        $this->dumpAutoload(optimize: false);
        $handler->clear();
    }

    private function run(
        bool $dev,
        bool $force
    ): void {
        $handler = $this->genesis->buildHandler;

        if ($dev) {
            $handler->compile = false;
        } elseif ($force) {
            $handler->compile = true;
        }

        $this->dumpAutoload(optimize: true);
        $handler->run();
    }

    private function dumpAutoload(
        bool $optimize
    ): void {
        $dir = $this->findComposerDir();
        $args = ['composer', 'dump-autoload'];

        if ($optimize) {
            $args[] = '--optimize';
        }

        $this->systemic->run($args, $dir);
    }

    protected function findComposerDir(): string
    {
        $fallback = $dir = Monarch::getPaths()->working;

        do {
            if (file_exists($dir . '/composer.json')) {
                return $dir;
            }

            $dir = dirname($dir);
        } while ($dir !== '/');

        return $fallback;
    }

    private function ensureCliSource(): void
    {
        $kingdom = Monarch::getKingdom();
        $mode = $kingdom->runtime->mode;

        if (
            !Monarch::getBuild()->compiled ||
            $mode !== RuntimeMode::Cli ||
            in_array('--from-source', Coercion::toArray($_SERVER['argv'] ?? []))
        ) {
            return;
        }

        $this->io->notice('Switching to source mode');
        $this->io->newLine();

        /** @var array<string> */
        $args = $_SERVER['argv'] ?? [];
        $args[] = '--from-source';

        $this->systemic->runScript($args);
        exit(0);
    }
}
