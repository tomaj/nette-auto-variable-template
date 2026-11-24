---
title: Configuration
description: Every way to register the factory and its variables in config.neon, the setup lines it accepts, and the same configuration in plain PHP.
---

# Configuration

`AutoVariableTemplateFactory` has no configuration section of its own. It is an ordinary service: you register it in the `services` section, give it Nette's template factory as the constructor argument and add the variables with `setup` lines.

## The two service entries

```neon
services:
	latte.templateFactory:
		autowired: no

	templateFactory:
		factory: Tomaj\Latte\AutoVariableTemplateFactory(@latte.templateFactory)
```

| Entry | What it does | Required |
|---|---|---|
| `latte.templateFactory: autowired: no` | Keeps Nette's `Nette\Bridges\ApplicationLatte\TemplateFactory` (registered by the Latte extension) as a service, but stops autowiring it. | Yes. Without it, two services implement `Nette\Application\UI\TemplateFactory` and the container fails with "Multiple services of type Nette\Application\UI\TemplateFactory found". |
| `templateFactory: factory: ...(@latte.templateFactory)` | Registers the decorator. Its only constructor argument is the factory that really creates the templates. The service name is your choice; `templateFactory` is used in all examples. | Yes |

Presenters get the template factory through `Presenter::injectPrimary()` and components through their presenter, so after these two entries every `$this->template` in the application comes from `AutoVariableTemplateFactory`. Code that asks for `Nette\Application\UI\TemplateFactory` by type (a mail sender, for example) gets it too.

## Registering variables

Each `registerVariable(name, value)` setup line adds one variable. The name is a `string` and becomes the variable name in the template (`{$name}`); the value is anything Nette DI can pass as an argument (`mixed`):

```neon
parameters:
	supportEmail: help@acme.test
	shop:
		name: Acme
		currencies: [EUR, CZK]

services:
	latte.templateFactory:
		autowired: no

	templateFactory:
		factory: Tomaj\Latte\AutoVariableTemplateFactory(@latte.templateFactory)
		setup:
			- registerVariable('appName', 'Acme Shop')                  # a literal
			- registerVariable('supportEmail', %supportEmail%)          # a parameter
			- registerVariable('shop', %shop%)                          # a whole parameter section, as an array
			- registerVariable('debug', %debugMode%)                    # a built-in parameter
			- registerVariable('settings', @App\Model\Settings)         # a service, by type
			- registerVariable('cart', @cart)                           # a service, by name
			- registerVariable('version', @App\Model\Settings::getVersion())  # a method result
			- registerVariable('menu', [home: 'Home', about: 'About us'])     # an array

	cart: App\Model\Cart
	- App\Model\Settings
```

Every template can then print them:

```latte
<title>{$appName}</title>
<a href="mailto:{$supportEmail}">{$supportEmail}</a> · {$shop['name']} accepts {implode(', ', $shop['currencies'])}
{if $debug}<p>Debug mode</p>{/if}
<p>Version {$version}, {$settings->version}, {$cart->count()} items</p>
<nav>{foreach $menu as $action => $label}<a n:href="$action">{$label}</a>{/foreach}</nav>
```

| Value | Evaluated | Note |
|---|---|---|
| Literal, array, `%parameter%` | When the container is compiled | Fixed for the whole request. |
| `@service` | When the factory service is created (usually when the first presenter is created and gets its template factory) | The same service instance is in every template; a service with request state (cart, current user) shows its state at render time, because templates call its methods when they render. |
| `@service::method()` | Once, when the factory service is created | The result is fixed for the request. For a value that must be computed per template, use [`onCreate`](/guide/on-create). |

Rules that follow from the code (`registerVariable()` stores the value in an array under its name):

- **The same name again replaces the value.** The last `registerVariable()` for a name wins, also across several config files (`common.neon`, `local.neon`), because Nette DI appends their setup lines in order. The [precedence example](/examples/precedence) registers `theme` twice.
- **Order is kept.** `getVariables()` returns the variables in the order they were first registered, and they are assigned to the template in that order.
- **Only templates created later get them.** Registering a variable changes nothing on templates that already exist.
- **Names are not checked.** Any string works as a name, but use a valid PHP variable name, or Latte cannot print it as `{$name}`.

## Running code for every template

`$onCreate` is a public array of callbacks; add one with a setup line. The callback can be a method of a service, a static method or any PHP callable Nette DI can express:

```neon
services:
	templateFactory:
		factory: Tomaj\Latte\AutoVariableTemplateFactory(@latte.templateFactory)
		setup:
			- registerVariable('currency', 'EUR')
			- '$onCreate[]' = [@App\Templates\TemplateSetup, 'setUp']

	- App\Templates\TemplateSetup
```

Note the quotes around `$onCreate[]`: NEON needs them, because the line starts with `$`. The handlers run after the registered variables are assigned; see [Running code: onCreate](/guide/on-create).

## Names Nette already uses

Nette's template factory sets these variables on every template, if the template class declares them (Nette's default template does): `$user`, `$baseUrl`, `$basePath`, `$flashes`, `$control` and `$presenter`. Registered variables are assigned after that, so registering one of these names **replaces Nette's value in every template**. The [precedence example](/examples/precedence) replaces `$basePath` on purpose. Unless that is what you want, pick other names.

The default template class declares these as typed properties (`string $basePath`, `array $flashes`, ...). A registered value of a different type, or `null`, makes `createTemplate()` throw a `TypeError` such as "Cannot assign int to property ...::$basePath of type string".

## Several factories

Nothing stops you from registering more than one `AutoVariableTemplateFactory`, for example one around another for a separate e-mail factory with its own variables. Only one service of the type may be autowired; set `autowired: no` on the others and pass them by name (`@mailTemplateFactory`).

## Plain PHP

Without Nette DI, create the factory with any `Nette\Application\UI\TemplateFactory` and call the same methods. Here `$latteTemplateFactory` is Nette's Latte template factory:

```php
use Nette\Application\UI\Template;
use Tomaj\Latte\AutoVariableTemplateFactory;

$factory = new AutoVariableTemplateFactory($latteTemplateFactory);
$factory->registerVariable('appName', 'Acme Shop')
    ->registerVariable('supportEmail', 'help@acme.test');
$factory->onCreate[] = function (Template $template): void {
    $template->greeting = 'Hello from ' . $template->appName;
};

echo $factory->createTemplate()->greeting;
```

<!-- snippet: output -->
```html
Hello from Acme Shop
```

The [without DI example](/examples/without-di) also builds Nette's Latte template factory by hand.
