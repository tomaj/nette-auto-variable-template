<?php

declare(strict_types=1);

namespace Tomaj\Latte;

use Nette\Application\UI\Control;
use Nette\Application\UI\Template;
use Nette\Application\UI\TemplateFactory;

/**
 * Decorates a template factory: every template it creates gets the registered variables,
 * then the onCreate handlers run.
 *
 * A registered variable is assigned only when the template class can take it: the class declares
 * the property, allows dynamic properties (#[\AllowDynamicProperties] on the class or a parent, as on
 * Nette's DefaultTemplate) or has __set(). Otherwise the variable is skipped for that template.
 */
final class AutoVariableTemplateFactory implements TemplateFactory
{
    /**
     * Called with every created template, after the registered variables are assigned.
     *
     * @var array<callable(Template): void>
     */
    public array $onCreate = [];

    /** @var array<string, mixed> */
    private array $variables = [];

    /**
     * Per template class: whether it takes any property (see acceptsAnyProperty()), computed once.
     *
     * @var array<class-string, bool>
     */
    private array $acceptsAnyProperty = [];

    public function __construct(
        private readonly TemplateFactory $innerTemplateFactory,
    ) {}

    #[\Override]
    public function createTemplate(?Control $control = null, ?string $class = null): Template
    {
        // the interface declares only $control; the Latte TemplateFactory (and similar decorators) also accept $class
        $template = $this->innerTemplateFactory->createTemplate($control, $class); // @phpstan-ignore arguments.count
        $this->assignVariables($template);
        foreach ($this->onCreate as $handler) {
            $handler($template);
        }
        return $template;
    }

    /**
     * Registers a variable for every template created from now on. A variable registered again replaces the value.
     */
    public function registerVariable(string $key, mixed $variable): self
    {
        $this->variables[$key] = $variable;
        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    private function assignVariables(Template $template): void
    {
        $class = $template::class;
        $acceptsAnyProperty = $this->acceptsAnyProperty[$class] ??= $this->acceptsAnyProperty($class);
        foreach ($this->variables as $key => $value) {
            if (!$acceptsAnyProperty && !property_exists($class, $key)) {
                continue; // assigning it would create a dynamic property, which PHP deprecates on this class
            }
            $template->$key = $value; // @phpstan-ignore property.dynamicName (assigning arbitrary template variables is the purpose of this class)
        }
    }

    /**
     * @param class-string $class
     */
    private function acceptsAnyProperty(string $class): bool
    {
        if (method_exists($class, '__set')) {
            return true;
        }
        for ($reflection = new \ReflectionClass($class); $reflection !== false; $reflection = $reflection->getParentClass()) {
            if ($reflection->getAttributes(\AllowDynamicProperties::class) !== []) {
                return true;
            }
        }
        return false;
    }
}
