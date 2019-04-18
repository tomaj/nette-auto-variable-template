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

    public function createTemplate(Control $control = null)
    {
        $template = $this->innerTemplateFactory->createTemplate($control);
        $this->assignVariables($template);
        $this->onCreate($template);
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
