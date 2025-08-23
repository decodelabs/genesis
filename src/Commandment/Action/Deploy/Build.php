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
        protected Genesis $genesis
    ) {
    }

    public function execute(
        Request $request,
    ): bool {
        $this->ensureCliSource();

        // Setup controller
        $handler = $this->genesis->buildHandler;

        if ($request->parameters->asBool('clear')) {
            // Clear
            $handler->clear();
        } else {
            // Run
            if ($request->parameters->asBool('dev')) {
                $handler->compile = false;
            } elseif ($request->parameters->asBool('force')) {
                $handler->compile = true;
            }


            $handler->run();
        }

        clearstatcache();

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        return true;
    }

    private function ensureCliSource(): void
    {
        $kingdom = Monarch::getKingdom();
        $mode = $kingdom->runtime->mode;

        if (
            !Monarch::getBuild()->compiled ||
            $mode !== RuntimeMode::Cli ||
            in_array('--from-source', Coercion::toArray($_SERVER['argv'] ?? [])) ||
            !class_exists(Systemic::class)
        ) {
            return;
        }

        $this->io->notice('Switching to source mode');
        $this->io->newLine();

        /** @var array<string> */
        $args = $_SERVER['argv'] ?? [];
        $args[] = '--from-source';

        $systemic = $kingdom->getService(Systemic::class);
        $systemic->runScript($args);
        exit(0);
    }
}
