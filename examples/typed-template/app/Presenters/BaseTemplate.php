<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\Settings;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;

/**
 * Declares the variables registered in config/common.neon, so every template class that extends it
 * accepts them (a class without #[AllowDynamicProperties] does not accept undeclared properties).
 */
abstract class BaseTemplate extends Template
{
    public string $appName;

    public Settings $settings;

    // set by Nette's template factory when the template class declares them
    public Presenter $presenter;

    public string $basePath;
}
