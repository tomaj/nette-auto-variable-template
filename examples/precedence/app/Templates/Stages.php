<?php

declare(strict_types=1);

namespace App\Templates;

use Nette\Application\UI\Template;

/**
 * Sets the same variables at different stages of creating a template.
 */
final class Stages
{
    /** onCreate of Nette's latte.templateFactory: runs before the registered variables are assigned */
    public function innerOnCreate(Template $template): void
    {
        $template->a = 'Nette onCreate';
        $template->z = 'Nette onCreate';
    }

    /** onCreate of AutoVariableTemplateFactory: runs after the registered variables are assigned */
    public function onCreate(Template $template): void
    {
        $template->b = 'onCreate';
        $template->c = 'onCreate';
    }
}
