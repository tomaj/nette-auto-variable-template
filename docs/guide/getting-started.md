---
title: Getting started
description: Install nette-auto-variable-template, register it in config.neon and use the variables in every Latte template of a Nette application.
---

# Getting started

This page takes a Nette application from zero to a layout that prints the application name and a settings service in every page, without passing them from any presenter. The code is the [hello world example](/examples/hello-world); every file below is included from it, and CI runs it on every change.

## Requirements

- PHP 8.4 or newer
- [nette/application](https://doc.nette.org/en/application) 3.2 or newer, with [Latte](https://latte.nette.org/) templates (`latte/latte` 3)
- Nette DI with the Latte extension, which every [nette/web-project](https://github.com/nette/web-project) has. Without DI, see [Without Nette DI](#without-nette-di).

## Installation

```bash
composer require tomaj/nette-auto-variable-template
```

The package has one class, `Tomaj\Latte\AutoVariableTemplateFactory`, and no configuration of its own.

## 1. Register the factory

Nette registers its Latte template factory as the `latte.templateFactory` service, and presenters and components get it through autowiring. Turn off its autowiring and register `AutoVariableTemplateFactory` around it, so presenters and components get the decorated factory instead:

<<< @/../examples/hello-world/config/common.neon{neon}

- `latte.templateFactory: autowired: no` keeps Nette's factory as a service (the decorator needs it) but stops autowiring it. Without this line, the container has two services of the type `Nette\Application\UI\TemplateFactory` and fails with "Multiple services of type ... found".
- `templateFactory` is the decorator. It takes Nette's factory as its only argument and is now the autowired template factory.
- Each `registerVariable()` setup line adds one variable: here a literal (`'Acme Shop'`) and a service (`@App\Model\Settings`). Parameters (`%supportEmail%`) and expressions work too. See [Configuration](/guide/configuration) for every option.

The settings service in this example is a plain value object:

<<< @/../examples/hello-world/app/Model/Settings.php{php}

## 2. Use the variables in templates

Every template created through the factory has the variables. The layout uses `$appName` and `$settings`:

<<< @/../examples/hello-world/app/Presenters/templates/@layout.latte{latte}

and so do the page templates:

<<< @/../examples/hello-world/app/Presenters/templates/Home/default.latte{latte}

The presenter only sets what belongs to the page:

<<< @/../examples/hello-world/app/Presenters/HomePresenter.php{php}

## 3. Run it

```bash
php -S localhost:8000 -t www
```

Opening `http://localhost:8000/` gives:

<<< @/../examples/hello-world/expected-output.txt{html}

The file above is what `php run.php` in `examples/hello-world` prints; it requests `/` and `/home/about` from `www/index.php` in a fresh PHP process each, as the built-in server would. CI compares it with the real output on every pull request.

## Without Nette DI

The factory takes any `Nette\Application\UI\TemplateFactory`. Here `$latteTemplateFactory` is Nette's `Nette\Bridges\ApplicationLatte\TemplateFactory` (the `latte.templateFactory` service), which you can also create by hand, see the [without DI example](/examples/without-di):

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

## Next steps

- [How it works](/guide/how-it-works): the order in which Nette's defaults, registered variables, `onCreate` handlers and presenters set values
- [Configuration](/guide/configuration): every way to register a variable in `config.neon`
- [Presenters and components](/guide/templates): layouts, component templates and templates for e-mails
- [Running code: onCreate](/guide/on-create): filters, functions and values computed per template
- [Examples](/examples/): seven runnable projects
