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

        $this->generateLoader($root, $vendor, $hubClass);
        $this->generateAnalysisLoader($root, $vendor, $hubClass, $type);

        $io->write('Genesis loader file generated');
    }

    protected function generateLoader(
        string $root,
        string $vendor,
        string $hubClass
    ): void {
        $contents =
            <<<PHP
            <?php

            /**
             * @package Genesis
             * @license http://opensource.org/licenses/MIT
             */

            declare(strict_types=1);

            \$root = '{$root}';

            if(file_exists(\$root . '/data/local/run/genesis.php')) {
                \$output = require_once \$root . '/data/local/run/genesis.php';

                if(\$output !== false) {
                    return;
                }
            }

            require_once './autoload.php';

            new DecodeLabs\Genesis(
                rootPath: \$root,
                hubClass: {$hubClass}::class
            )->run();
            PHP;

        // Test existing to avoid re-generation
        if (
            file_exists($vendor . '/genesis.php') &&
            file_get_contents($vendor . '/genesis.php') === $contents
        ) {
            return;
        }

        file_put_contents($vendor . '/genesis.php', $contents);
    }

    protected function generateAnalysisLoader(
        string $root,
        string $vendor,
        string $hubClass,
        string $type
    ): void {
        $mode = match ($type) {
            'composer-plugin',
            'library' => 'Library',
            default => 'Project',
        };

        $contents =
            <<<PHP
            <?php

            /**
             * @package Genesis
             * @license http://opensource.org/licenses/MIT
             */

            declare(strict_types=1);

            require_once './autoload.php';

            new DecodeLabs\Genesis(
                rootPath: '{$root}',
                hubClass: {$hubClass}::class,
                analysisMode: DecodeLabs\Genesis\AnalysisMode::{$mode}
            );
            PHP;

        file_put_contents($vendor . '/genesis-analyze.php', $contents);
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
