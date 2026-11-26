---
title: FAQ
description: Frequently asked questions about nette-auto-variable-template - autowiring errors, Nette's own variables, translators, performance, typed templates and more.
---

# FAQ

## The container fails with "Multiple services of type Nette\Application\UI\TemplateFactory found"

Nette's `latte.templateFactory` is still autowired. Add `autowired: no` to it, as in [Configuration](/guide/configuration#the-two-service-entries). Only one service of a type can be autowired.

## A variable is undefined in one template, but works in others

That template was not created through the decorated factory. Typical causes: a service that gets `@latte.templateFactory` by name instead of the autowired `Nette\Application\UI\TemplateFactory`, a template rendered directly with `Latte\Engine`, or a template created before the variable was registered. See [Templates that do not get the variables](/guide/templates#templates-that-do-not-get-the-variables).

## Can a presenter override a registered variable?

Yes. The presenter assigns its variables after the template was created, so `$this->template->appName = 'Admin'` wins for that presenter's template. The [precedence example](/examples/precedence) shows the whole order.

## Can I register `$user` or `$basePath`?

You can, and the registered value then replaces Nette's in every template, because registered variables are assigned after Nette's defaults. Usually that is a mistake; pick another name. With Nette's default template class the value must also match the declared type (`string` for `$basePath`), otherwise `createTemplate()` throws a `TypeError`.

## How do I add a filter or a Latte function to every template?

Add an [`onCreate` handler](/guide/on-create) that calls `$template->addFilter()` or `$template->addFunction()`. The [onCreate example](/examples/on-create) adds a `money` filter. For filters that do not depend on the template, a Latte extension registered in the `latte: extensions:` section of `config.neon` works too.

## How do I use a translator in every template?

Add an `onCreate` handler that calls `$template->setTranslator($translator)` (a method of `Nette\Bridges\ApplicationLatte\Template`), or register Latte's `Latte\Essential\TranslatorExtension` in the `latte: extensions:` section of `config.neon`, which does not need this package at all.

## Is a registered service created for every template?

No. A service is created once, when the factory service is created, and the same instance is assigned to every template. Assigning a variable is a property write per variable per template, which is negligible next to rendering.

## Does registering a service slow down every request?

Only by the cost of creating the service. A service registered with `@...` is created with the template factory, on every request that renders a template, even when no template prints it. That is free for a light service. For one with an expensive constructor, mark it `lazy: true` (PHP 8.4, nette/di 3.2.4+): the template gets a lazy ghost and the constructor runs only when a template first uses it. See [Expensive services](/guide/configuration#expensive-services-lazy-true) and the [lazy service example](/examples/lazy-service).

## Is the value computed again for every template?

Registered values are fixed: a literal, a parameter or the result of `@service::method()` is evaluated once. For a value that must be computed per template, use `onCreate`; for a value that changes during the request, register the service and call its method in the template (`{$cart->count()}`).

## Does it work with typed template classes and `{templateType}`?

Yes. The class gets the registered variables it declares as properties; the ones it does not declare are skipped for it, without a deprecation (unless it has `#[\AllowDynamicProperties]`, then it gets all of them). See [Custom template classes](/guide/template-classes).

## Does it work without Nette DI, or outside presenters?

Yes. The constructor takes any `Nette\Application\UI\TemplateFactory`; create Nette's Latte template factory by hand and call `createTemplate()`. See the [without DI example](/examples/without-di).

## Can I remove a registered variable?

No, there is no method for it. Register the name again with another value, or set it on the template in a presenter or `onCreate` handler.

## Can I extend the class?

No, it is `final` since 4.0. Configure it with `registerVariable()` and `onCreate`, or write your own `TemplateFactory` that wraps it. See [Upgrading to 4.0](/upgrade).

## Which versions are supported?

4.x needs PHP 8.4 or newer and nette/application 3.2 or newer, and is tested on PHP 8.4, 8.5 and 8.6. 3.x supports PHP 8.1+; see the [security policy](https://github.com/tomaj/nette-auto-variable-template/blob/master/SECURITY.md) for which versions get fixes.
