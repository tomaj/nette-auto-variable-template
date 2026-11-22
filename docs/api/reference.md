---
title: API reference
description: The public API of Tomaj\Latte\AutoVariableTemplateFactory.
---

# API reference

## `Tomaj\Latte\AutoVariableTemplateFactory`

<!-- snippet: skip -->
```php
final class AutoVariableTemplateFactory implements Nette\Application\UI\TemplateFactory
```

A decorator around another template factory: it lets the inner factory create the template, assigns the registered variables to it and calls the `onCreate` handlers. The class is `final`; configure it instead of extending it.

### `__construct()`

<!-- snippet: skip -->
```php
public function __construct(Nette\Application\UI\TemplateFactory $innerTemplateFactory)
```

`$innerTemplateFactory` creates the templates, usually Nette's `latte.templateFactory` service (`Nette\Bridges\ApplicationLatte\TemplateFactory`).

### `createTemplate()`

<!-- snippet: skip -->
```php
public function createTemplate(?Nette\Application\UI\Control $control = null, ?string $class = null): Nette\Application\UI\Template
```

Creates a template:

1. calls `$innerTemplateFactory->createTemplate($control, $class)` (Nette's Latte factory accepts the template class as the second argument);
2. assigns every registered variable to the template, replacing a value the inner factory set under the same name;
3. calls every `onCreate` handler with the template;
4. returns the template.

Presenters and components call it through `Control::createTemplate()`; you rarely call it yourself.

### `registerVariable()`

<!-- snippet: skip -->
```php
public function registerVariable(string $key, mixed $variable): self
```

Registers a variable for every template created from now on and returns the factory, so calls can be chained. Registering the same `$key` again replaces the value. Templates that already exist are not changed.

```php
$factory->registerVariable('appName', 'My Application')
    ->registerVariable('settings', new ArrayObject(['theme' => 'dark']));
```

### `getVariables()`

<!-- snippet: skip -->
```php
public function getVariables(): array
```

Returns the registered variables as `array<string, mixed>`, in the order they were registered.

```php
$factory->registerVariable('appName', 'My Application');
var_dump($factory->getVariables() === ['appName' => 'My Application']);
```

<!-- snippet: output -->
```html
bool(true)
```

### `$onCreate`

<!-- snippet: skip -->
```php
/** @var array<callable(Nette\Application\UI\Template): void> */
public array $onCreate = [];
```

Callbacks called by `createTemplate()` for every new template, after the variables are assigned, in the order of the array. Add one with `$factory->onCreate[] = $callback` or, in `config.neon`, with the setup line `- '$onCreate[]' = [@service, 'method']`.

```php
use Nette\Application\UI\Template;

$factory->onCreate[] = function (Template $template): void {
    $template->now = new DateTimeImmutable();
};
```
