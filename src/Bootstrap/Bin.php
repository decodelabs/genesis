<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Bootstrap;

require_once dirname(__DIR__).'/Bootstrap.php';

use DecodeLabs\Genesis;
use DecodeLabs\Genesis\Bootstrap;
use DecodeLabs\Genesis\Hub;
use RuntimeException;

class Bin implements Bootstrap
{
    /**
     * @var class-string<Hub>
     */
    protected(set) string $hubClass;
    protected(set) string $rootPath;

    /**
     * @param class-string<Hub> $hubClass
     */
    public function __construct(
        string $hubClass,
        ?string $rootPath = null
    ) {
        $this->hubClass = $hubClass;

        if ($rootPath === null) {
            $rootPath = getcwd();

            if($rootPath === false) {
                throw new RuntimeException('Unable to determine current working directory');
            }
        }

        $this->rootPath = $rootPath;
    }

    public function getBuildStrategy(): ?string
    {
        return null;
    }

    public function initializeOnly(): void
    {
        Genesis::bootstrap($this);
    }

    public function run(): never
    {
        $kernel = Genesis::bootstrap($this);
        $kernel->run();
        $kernel->shutdown();
    }
}
