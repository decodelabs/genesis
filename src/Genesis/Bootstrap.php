<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis;

interface Bootstrap
{
    /**
     * @var class-string<Hub>
     */
    public string $hubClass { get; }
    public string $rootPath { get; }

    public function initializeOnly(): void;
    public function run(): never;
}
