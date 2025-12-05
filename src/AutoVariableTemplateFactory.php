<?php

namespace Tomaj\Latte;

use Nette\Application\UI\Control;
use Nette\Application\UI\Template;
use Nette\Application\UI\TemplateFactory;
use Nette\SmartObject;

class AutoVariableTemplateFactory implements TemplateFactory
{
    use SmartObject;

    /** @var array<string, mixed> */
    private array $variables = [];

    public $onCreate = [];

    private TemplateFactory $innerTemplateFactory;

    public function __construct(TemplateFactory $innerTemplateFactory)
    {
        $this->innerTemplateFactory = $innerTemplateFactory;
    }

    public function createTemplate(?Control $control = null, ?string $class = null): Template
    {
        $template = $this->innerTemplateFactory->createTemplate($control, $class);
        $this->assignVariables($template);
        $this->onCreate($template);
        return $template;
    }

    public function registerVariable(string $key, $variable): self
    {
        $this->variables[$key] = $variable;
        return $this;
    }

    private function assignVariables(Template $template): void
    {
        foreach ($this->variables as $key => $value) {
            $template->$key = $value;
        }
    }

    public function getVariables(): array
    {
        return $this->variables;
    }
}
