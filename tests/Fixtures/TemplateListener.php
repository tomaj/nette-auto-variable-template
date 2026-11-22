<?php

declare(strict_types=1);

namespace Tomaj\Latte\Test\Fixtures;

use Nette\Application\UI\Template;

/**
 * Registered as an onCreate handler in tests/Fixtures/config.neon.
 */
final class TemplateListener
{
    /** @var list<Template> */
    public array $templates = [];

    public function templateCreated(Template $template): void
    {
        $this->templates[] = $template;
        $template->createdBy = 'listener'; // @phpstan-ignore property.notFound (templates take any variable)
    }
}
