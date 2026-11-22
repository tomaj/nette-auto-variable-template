<?php

declare(strict_types=1);

namespace Tomaj\Latte\Test\Fixtures;

use Nette\Bridges\ApplicationLatte\Template;

/**
 * A custom template class, created through createTemplate($control, AppTemplate::class).
 * It declares every registered variable: unlike DefaultTemplate, a custom class does not allow dynamic properties.
 */
final class AppTemplate extends Template
{
    public string $appName = '';

    public ?Settings $settings = null;

    public string $createdBy = '';
}
