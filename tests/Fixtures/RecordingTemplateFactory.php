<?php

declare(strict_types=1);

namespace Tomaj\Latte\Test\Fixtures;

use Nette\Application\UI\Control;
use Nette\Application\UI\Template;
use Nette\Application\UI\TemplateFactory;

/**
 * Inner factory that records its arguments and returns a new template: an instance of $class when given
 * (a class with a constructor without arguments), otherwise a StubTemplate with optional preset variables.
 */
final class RecordingTemplateFactory implements TemplateFactory
{
    /** @var list<array{?Control, ?string}> */
    public array $calls = [];

    /** @var list<Template> */
    public array $created = [];

    /**
     * @param array<string, mixed> $presets variables the inner factory sets itself, like Nette's $basePath or $user
     */
    public function __construct(private readonly array $presets = []) {}

    #[\Override]
    public function createTemplate(?Control $control = null, ?string $class = null): Template
    {
        $this->calls[] = [$control, $class];
        if ($class !== null) {
            $template = new $class();
            \assert($template instanceof Template);
            $this->created[] = $template;
            return $template;
        }
        $template = new StubTemplate();
        foreach ($this->presets as $name => $value) {
            $template->$name = $value; // @phpstan-ignore property.dynamicName (the stub stands in for Nette's template, which takes any variable)
        }
        $this->created[] = $template;
        return $template;
    }
}
