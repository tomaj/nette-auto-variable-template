<?php

declare(strict_types=1);

namespace App\Templates;

use Nette\Application\UI\Presenter;
use Nette\Application\UI\Template;
use Nette\Bridges\ApplicationLatte\Template as LatteTemplate;

/**
 * Called by AutoVariableTemplateFactory for every new template, after the registered variables are assigned.
 */
final class TemplateSetup
{
    public function setUp(Template $template): void
    {
        if (!$template instanceof LatteTemplate) {
            return;
        }

        // the registered $currency is already in the template, so the filter can use it
        $currency = $template->currency;
        $template->addFilter('money', static fn(float $amount): string => number_format($amount, 2, '.', ' ') . ' ' . $currency);

        // a value that differs per template: the CSS class of the page or the name of the component
        $control = $template->control ?? null;
        $template->cssClass = match (true) {
            $control instanceof Presenter => 'page-' . strtolower((string) $control->getName()) . '-' . $control->getAction(),
            $control !== null => 'component-' . $control->getName(),
            default => 'standalone',
        };
    }
}
