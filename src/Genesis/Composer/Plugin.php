<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Repository\InstalledRepository;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @return array<string,string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'process',
            ScriptEvents::POST_UPDATE_CMD => 'process',
        ];
    }

    public function process(
        Event $event
    ): void {
        $repo = $event->getComposer()->getRepositoryManager()->getLocalRepository();
        $repo = new InstalledRepository([$repo]);
        $genesisInstalled = count($repo->findPackagesWithReplacersAndProviders('decodelabs/genesis')) > 0;

        if (!$genesisInstalled) {
            return;
        }

        $vendor = $event->getComposer()->getConfig()->get('vendor-dir');
        $root = dirname($vendor);
        $package = $event->getComposer()->getPackage();
        $extra = $package->getExtra()['genesis'] ?? [];
        $hubClass = null;

        if (is_array($extra)) {
            $hubClass = $extra['hub'] ?? null;
        }

        $type = $package->getType();
        $io = $event->getIO();

        if (!is_string($hubClass)) {
            if ($type === 'project') {
                $io->write('<warning>Genesis hub class not found in composer.json extra "genesis.hub"</warning>');
            }

            return;
        }

        require_once __DIR__ . '/Generator.php';

        $generator = new Generator(
            root: $root,
            vendor: $vendor,
            hubClass: $hubClass,
            type: $type
        );

        $generator->generateLoader();
        $generator->generateAnalysisLoader();

        $io->write('Genesis loader file generated');
    }



    public function activate(
        Composer $composer,
        IOInterface $io
    ): void {
    }

    public function deactivate(
        Composer $composer,
        IOInterface $io
    ): void {
    }

    public function uninstall(
        Composer $composer,
        IOInterface $io
    ): void {
    }
}
