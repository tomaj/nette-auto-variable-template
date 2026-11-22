---
title: Getting started
description: Install nette-auto-variable-template, register it in config.neon and use the variables in every Latte template.
---

# Getting started

## Requirements

- PHP 8.4 or newer
- [nette/application](https://doc.nette.org/en/application) 3.2 or newer with [Latte](https://latte.nette.org/) templates

## Installation

```bash
composer require tomaj/nette-auto-variable-template
```

## Configuration

Nette registers its Latte template factory as the `latte.templateFactory` service. Turn off its autowiring and register `Tomaj\Latte\AutoVariableTemplateFactory` around it, so presenters and components get the decorated factory:

```neon
services:
	latte.templateFactory:
		autowired: no

	templateFactory:
		factory: Tomaj\Latte\AutoVariableTemplateFactory(@latte.templateFactory)
		setup:
			- registerVariable('appName', 'My Application')
			- registerVariable('settings', @App\Model\SettingsService)
			- registerVariable('supportEmail', %supportEmail%)
```

Each `registerVariable()` line adds one variable: a literal, a parameter or a service. Registering the same name again replaces the value.

## Using the variables

Every template created through the factory has the variables, in presenters and in components:

```latte
<h1>{$appName}</h1>
<p>Version: {$settings->getVersion()}</p>
<a href="mailto:{$supportEmail}">Contact us</a>
```

The factory assigns the variables when the template is created, so a presenter or component can still change them for its own template:

```php
$template->appName = 'Admin – ' . $template->appName;
```

## Without Nette DI

The factory takes any `Nette\Application\UI\TemplateFactory`. Here `$latteTemplateFactory` is Nette's `Nette\Bridges\ApplicationLatte\TemplateFactory` (the `latte.templateFactory` service):

```php
use Tomaj\Latte\AutoVariableTemplateFactory;

$factory = new AutoVariableTemplateFactory($latteTemplateFactory);
$factory->registerVariable('appName', 'My Application')
    ->registerVariable('year', 2026);

$template = $factory->createTemplate();
echo $template->appName, ' ', $template->year;
```

<!-- snippet: output -->
```html
My Application 2026
```

## Running code for every template

`onCreate` is an array of callbacks. Each one is called with every new template, after the registered variables are assigned, in the order they were added. Use it for values that must be computed per template, or for filters and functions:

```php
use Nette\Application\UI\Template;
use Nette\Bridges\ApplicationLatte\Template as LatteTemplate;

$factory->onCreate[] = function (Template $template): void {
    $template->generatedAt = new DateTimeImmutable();

    if ($template instanceof LatteTemplate) {
        $template->addFilter('money', fn (float $amount): string => number_format($amount, 2) . ' €');
    }
};
```

In `config.neon`, add a handler with a setup line. The callback can be a service method:

```neon
services:
	templateFactory:
		factory: Tomaj\Latte\AutoVariableTemplateFactory(@latte.templateFactory)
		setup:
			- registerVariable('appName', 'My Application')
			- '$onCreate[]' = [@App\Templates\TemplateFilters, 'register']
```

## Good to know

- **Names Nette already uses.** Nette's template factory sets `$user`, `$baseUrl`, `$basePath`, `$flashes`, `$control` and `$presenter`. A registered variable with the same name replaces Nette's value in every template, so pick other names unless that is what you want.
- **Custom template classes.** Nette's default template class accepts any variable. A [custom template class](https://doc.nette.org/en/application/templates#toc-type-safe-templates) does not allow dynamic properties, so declare every registered variable as a property there, or PHP reports the assignment as deprecated.
- **No secrets.** Every template gets every registered variable, including component templates from third-party packages. Do not register passwords, API keys or configuration arrays that contain them, and register only services that all templates may use.

## Next steps

- [API reference](/api/reference): the constructor, `createTemplate()`, `registerVariable()`, `getVariables()` and `onCreate`
- [Changelog](/changelog), and [UPGRADE-4.0.md](https://github.com/tomaj/nette-auto-variable-template/blob/master/UPGRADE-4.0.md) for upgrading from 3.x
