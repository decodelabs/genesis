<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Composer;

class Generator
{
    public function __construct(
        private readonly string $root,
        private readonly string $vendor,
        private readonly string $hubClass,
        private readonly string $type
    ) {
    }

    public function generateLoader(): void
    {
        $contents =
            <<<PHP
            <?php

            /**
             * @package Genesis
             * @license http://opensource.org/licenses/MIT
             */

            declare(strict_types=1);

            \$root = '{$this->root}';

            if(file_exists(\$root . '/data/local/run/genesis.php')) {
                \$output = require_once \$root . '/data/local/run/genesis.php';

                if(\$output !== false) {
                    return;
                }
            }

            require_once __DIR__ . '/autoload.php';

            new DecodeLabs\Genesis(
                rootPath: \$root,
                hubClass: {$this->hubClass}::class
            )->run();
            PHP;

        // Test existing to avoid re-generation
        if (
            file_exists($this->vendor . '/genesis.php') &&
            file_get_contents($this->vendor . '/genesis.php') === $contents
        ) {
            return;
        }

        file_put_contents($this->vendor . '/genesis.php', $contents);
    }

    public function generateAnalysisLoader(): void
    {
        $mode = match ($this->type) {
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

            require_once __DIR__ . '/autoload.php';

            new DecodeLabs\Genesis(
                rootPath: '{$this->root}',
                hubClass: {$this->hubClass}::class,
                analysisMode: DecodeLabs\Genesis\AnalysisMode::{$mode}
            );
            PHP;

        file_put_contents($this->vendor . '/genesis-analyze.php', $contents);
    }
}
