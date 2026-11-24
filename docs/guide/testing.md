---
title: Testing
description: Test that your Nette application registers the template variables and that presenters and components render them, with PHPUnit.
---

# Testing your integration

Three things are worth a test in an application that uses the factory:

1. the container autowires `AutoVariableTemplateFactory` as the template factory (the `autowired: no` line is in place),
2. the variables you expect are registered, with the right values,
3. a presenter renders them.

The [testing example](/examples/testing) does all three with PHPUnit, using the application's real `config/common.neon`:

<<< @/../examples/testing/tests/TemplateVariablesTest.php{php}

`php run.php` runs PHPUnit and prints:

<<< @/../examples/testing/expected-output.txt{text}

## Without a container

For a unit test of an `onCreate` handler, or of code that renders a template, build the factory by hand around Nette's Latte template factory. `$latteTemplateFactory` here is a `Nette\Bridges\ApplicationLatte\TemplateFactory` (see the [without DI example](/examples/without-di) for how to create one):

```php
use Nette\Application\UI\Template;
use Tomaj\Latte\AutoVariableTemplateFactory;

$factory = new AutoVariableTemplateFactory($latteTemplateFactory);
$factory->registerVariable('appName', 'Test Shop');
$factory->onCreate[] = static function (Template $template): void {
    $template->title = strtoupper($template->appName);
};

$template = $factory->createTemplate();
var_dump($template->appName === 'Test Shop', $template->title === 'TEST SHOP');
```

<!-- snippet: output -->
```html
bool(true)
bool(true)
```

## Testing that a variable is not leaked

If a value must never reach templates (see [Security](/guide/security)), assert that `getVariables()` does not contain it:

```php
$factory->registerVariable('appName', 'Acme Shop');

var_dump(array_key_exists('apiKey', $factory->getVariables()));
```

<!-- snippet: output -->
```html
bool(false)
```
