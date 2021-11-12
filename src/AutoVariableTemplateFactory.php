<?php

namespace Tomaj\Latte;

use Nette\Application\UI\Control;
use Nette\Application\UI\ITemplate;
use Nette\Application\UI\ITemplateFactory;
use Nette\SmartObject;

class AutoVariableTemplateFactory implements ITemplateFactory
{
    use SmartObject;

    private $variables = [];

    public $onCreate = [];

    /** @var ITemplateFactory  */
    private $innerTemplateFactory;

    public function __construct(ITemplateFactory $innerTemplateFactory)
    {
        $this->innerTemplateFactory = $innerTemplateFactory;
    }

    public function createTemplate(Control $control = null, string $class = null): ITemplate
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

    private function assignVariables(ITemplate $template): void
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
