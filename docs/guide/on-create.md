---
title: 'Running code: onCreate'
description: Run your own callbacks for every template created through AutoVariableTemplateFactory, after the registered variables are assigned, for filters, functions and values computed per template.
---

# Running code: `onCreate`

`$onCreate` is a public array of callables on the factory:

<!-- snippet: skip -->
```php
/** @var array<callable(Nette\Application\UI\Template): void> */
public array $onCreate = [];
```

`createTemplate()` calls each of them with the new template, **after** the registered variables are assigned, in the order of the array. The return value is ignored. Use it for what a fixed value cannot do:

- add Latte filters and functions to every template,
- compute a value per template (from the control, the presenter or the current request),
- read the registered variables and derive something from them.

## In PHP

```php
use Nette\Application\UI\Template;
use Nette\Bridges\ApplicationLatte\Template as LatteTemplate;

$factory->onCreate[] = function (Template $template): void {
    $template->generatedAt = new DateTimeImmutable('2026-11-24 10:00');

    if ($template instanceof LatteTemplate) {
        $template->addFilter('money', fn (float $amount): string => number_format($amount, 2) . ' €');
    }
};

$template = $factory->createTemplate();
echo $template->appName, ', ', $template->generatedAt->format('Y-m-d');
```

<!-- snippet: output -->
```html
My Application, 2026-11-24
```

The parameter type is the interface `Nette\Application\UI\Template`. `addFilter()`, `addFunction()` and `getLatte()` exist only on `Nette\Bridges\ApplicationLatte\Template`, which Nette's factory always returns; check for it as above, so PHPStan knows the method exists.

## In config.neon

Add a service method as the handler with a setup line. From the [onCreate example](/examples/on-create):

<<< @/../examples/on-create/config/common.neon{neon}

<<< @/../examples/on-create/app/Templates/TemplateSetup.php{php}

The handler reads `$currency`, which was registered before it, adds a `money` filter that uses it, and sets `$cssClass` from the control the template belongs to. The page and the component print:

<<< @/../examples/on-create/expected-output.txt{html}

## Order

- All registered variables are assigned before the first handler runs, so every handler can read them.
- Handlers run in the order they were added; a later handler sees what earlier ones set.
- The `onCreate` of Nette's own `latte.templateFactory` service runs earlier, inside the inner factory, before the registered variables. A registered variable therefore replaces a value set there. See [How it works](/guide/how-it-works#which-value-wins).
- A presenter or component assigns its own variables after `createTemplate()` returns, so it can still override what a handler set.

## Exceptions

`createTemplate()` does not catch anything: an exception thrown by a handler propagates to the caller (for a presenter, the request fails with that exception) and the handlers after it do not run.

## onCreate or registerVariable?

| Need | Use |
|---|---|
| The same value or service in every template | `registerVariable()` |
| A filter or function in every template | `onCreate` (or Latte's own extension mechanism) |
| A value that depends on the template, control or presenter | `onCreate` |
| A value computed once per request, cheap | `registerVariable()` with a service and a method call in the template (`{$settings->version}`) |
