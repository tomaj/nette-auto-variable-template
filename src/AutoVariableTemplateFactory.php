<?php

namespace Tomaj\Latte;

use Nette\Application\UI\Control;
use Nette\Application\UI\ITemplate;
use Nette\Application\UI\ITemplateFactory;

class AutoVariableTemplateFactory implements ITemplateFactory
{
    private $variables = [];

    /** @var ITemplateFactory  */
    private $innerTemplateFactory;

    public function __construct(ITemplateFactory $innerTemplateFactory)
    {
        $this->innerTemplateFactory = $innerTemplateFactory;
    }

    public function createTemplate(Control $control = null)
    {
        $template = $this->innerTemplateFactory->createTemplate($control);
        $this->assignVariables($template);
        return $template;
    }

    public function registerVariable($key, $variable)
    {
        $this->variables[$key] = $variable;
        return $this;
    }

    private function assignVariables(ITemplate $template)
    {
        foreach ($this->variables as $key => $value) {
            $template->$key = $value;
        }
    }
}
