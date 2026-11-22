<?php

declare(strict_types=1);

namespace Tomaj\Latte;

use Nette\Application\UI\Control;
use Nette\Application\UI\Template;
use Nette\Application\UI\TemplateFactory;
use Nette\SmartObject;

/**
 * @method void onCreate(Template $template)
 */
class AutoVariableTemplateFactory implements TemplateFactory
{
    use SmartObject;

    /** @var array<string, mixed> */
    private array $variables = [];

    /** @var array<callable(Template): void> */
    public $onCreate = [];

    public function __construct(private TemplateFactory $innerTemplateFactory) {}

    #[\Override]
    public function createTemplate(?Control $control = null, ?string $class = null): Template
    {
        // the interface declares only $control; the Latte TemplateFactory (and similar decorators) also accept $class
        $template = $this->innerTemplateFactory->createTemplate($control, $class); // @phpstan-ignore arguments.count
        $this->assignVariables($template);
        $this->onCreate($template);
        return $template;
    }

    /**
     * @param mixed $variable
     */
    public function registerVariable(string $key, $variable): self
    {
        $this->variables[$key] = $variable;
        return $this;
    }

    private function assignVariables(Template $template): void
    {
        foreach ($this->variables as $key => $value) {
            $template->$key = $value; // @phpstan-ignore property.dynamicName (assigning arbitrary template variables is the purpose of this class)
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getVariables(): array
    {
        return $this->variables;
    }
}
