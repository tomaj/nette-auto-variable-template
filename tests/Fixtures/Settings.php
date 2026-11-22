<?php

declare(strict_types=1);

namespace Tomaj\Latte\Test\Fixtures;

/**
 * A service registered as a template variable in tests/Fixtures/config.neon.
 */
final class Settings
{
    public function getVersion(): string
    {
        return '4.0';
    }
}
