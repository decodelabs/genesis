<?php

/**
 * @package Genesis
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Genesis\Environment;

use DecodeLabs\Enumerable\Backed\ValueString;
use DecodeLabs\Enumerable\Backed\ValueStringTrait;

enum Mode: string implements ValueString
{
    use ValueStringTrait;

    case Development = 'development';
    case Testing = 'testing';
    case Production = 'production';

    public function isDevelopment(): bool
    {
        return $this === self::Development;
    }

    public function isTesting(): bool
    {
        return
            $this === self::Development ||
            $this === self::Testing;
    }

    public function isProduction(): bool
    {
        return $this === self::Production;
    }
}
