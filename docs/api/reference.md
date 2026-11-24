---
title: API reference
description: The public API of Tomaj\Latte\AutoVariableTemplateFactory.
---

# API reference

The package has one class. It has no dependencies of its own besides `nette/application`, and it is the whole public API.

| Member | Kind | Summary |
|---|---|---|
| [`__construct(TemplateFactory $innerTemplateFactory)`](#construct) | constructor | wraps the factory that creates the templates |
| [`createTemplate(?Control $control = null, ?string $class = null): Template`](#createtemplate) | method | creates a template, assigns the variables, runs `onCreate` |
| [`registerVariable(string $key, mixed $variable): self`](#registervariable) | method | registers a variable for every template created from now on |
| [`getVariables(): array`](#getvariables) | method | the registered variables |
| [`$onCreate`](#oncreate) | public property | callbacks run for every new template |

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

`$innerTemplateFactory` creates the templates, usually Nette's `latte.templateFactory` service (`Nette\Bridges\ApplicationLatte\TemplateFactory`). It is stored in a private `readonly` property; there is no getter. Any implementation of the interface works, including another decorator.

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

| Parameter | Type | Default | Meaning |
|---|---|---|---|
| `$control` | `?Nette\Application\UI\Control` | `null` | The presenter or component the template belongs to. Passed on unchanged; Nette's factory uses it for `$control`, `$presenter` and `$flashes`. |
| `$class` | `?string` | `null` | The template class to create. Passed on unchanged as the second argument, which Nette's Latte factory accepts although the interface declares only `$control`; `null` means the factory's default class. |

**Returns** the template created by the inner factory (the same object, not a copy).

**Throws** nothing itself. It propagates:

- what the inner factory throws, such as `Nette\InvalidArgumentException` from Nette's factory when `$class` does not exist or does not extend `Nette\Bridges\ApplicationLatte\Template`;
- a `TypeError` when a registered value does not fit a typed property of the template class (for example `null` for `string $basePath`, see [Custom template classes](/guide/template-classes#types-must-match));
- any exception thrown by an `onCreate` handler; the handlers after it do not run.

Assigning a registered variable to a template class without `#[\AllowDynamicProperties]` that does not declare it raises PHP's "Creation of dynamic property is deprecated" notice.

Presenters and components call it through `Control::createTemplate()`; you rarely call it yourself.

### `registerVariable()`

<!-- snippet: skip -->
```php
public function registerVariable(string $key, mixed $variable): self
```

| Parameter | Type | Meaning |
|---|---|---|
| `$key` | `string` | The variable name in the template (`{$key}`). Not validated; use a valid PHP variable name. |
| `$variable` | `mixed` | The value. Objects are assigned by reference to the same instance in every template. |

Registers a variable for every template created from now on and returns the factory (`$this`), so calls can be chained. Registering the same `$key` again replaces the value but keeps its original position in the order. Templates that already exist are not changed. There is no method to remove a variable.

```php
$factory->registerVariable('appName', 'My Application')
    ->registerVariable('settings', new ArrayObject(['theme' => 'dark']));
```

### `getVariables()`

<!-- snippet: skip -->
```php
public function getVariables(): array
```

Returns the registered variables as `array<string, mixed>` (name => value), in the order they were first registered. The returned array is a copy; changing it does not change the factory.

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

`$onCreate` is a plain typed property (since 4.0 there is no `Nette\SmartObject` magic): assigning a non-array throws a `TypeError`, and there is no `$factory->onCreate($template)` method. See [Running code: onCreate](/guide/on-create).

## Not part of the API

- The `private` method `assignVariables()` and the `private` properties may change in any release.
- The class is `final`. Configure it, or decorate it with your own `Nette\Application\UI\TemplateFactory`, instead of extending it ([Upgrading to 4.0](/upgrade#the-factory-is-final)).
