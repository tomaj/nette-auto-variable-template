<?php

declare(strict_types=1);

namespace App\Model;

final readonly class Settings
{
    public function __construct(
        public string $version,
        public string $supportEmail,
    ) {}
}
