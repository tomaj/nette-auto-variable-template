---
title: Custom template classes
description: Use registered variables with type-safe Nette template classes and {templateType}; a class gets only the registered variables it declares.
---

# Custom template classes

Nette's default template class, `Nette\Bridges\ApplicationLatte\DefaultTemplate`, has `#[\AllowDynamicProperties]`, so it accepts any variable, including the registered ones. A [type-safe template class](https://doc.nette.org/en/application/templates#toc-type-safe-templates) that extends `Nette\Bridges\ApplicationLatte\Template` does not: since PHP 8.2, assigning a property the class does not declare is deprecated. So `createTemplate()` assigns a registered variable only when the template class can take it:

- the class declares the property (itself or in a parent class), or
- the class or one of its parents has `#[\AllowDynamicProperties]` (as `DefaultTemplate` has), or
- the class has a `__set()` method.

Otherwise the variable is skipped for that template, without a deprecation or an error. Whether a class allows dynamic properties or has `__set()` is looked up once per template class, not on every request.

## Declare the registered variables

Put the registered variables on a base class and extend it. From the [typed template example](/examples/typed-template):

<<< @/../examples/typed-template/app/Presenters/BaseTemplate.php{php}

<<< @/../examples/typed-template/app/Presenters/HomeTemplate.php{php}

Nette uses `HomeTemplate` for `HomePresenter` because of its name (`HomePresenter` → `HomeTemplate`, or `HomeDefaultTemplate` for the `default` action); components follow the same rule (`MenuControl` → `MenuTemplate`). The `{templateType}` tag gives Latte and your IDE the types:

<<< @/../examples/typed-template/app/Presenters/templates/Home/default.latte{latte}

Nette's factory sets `$user`, `$baseUrl`, `$basePath`, `$flashes`, `$control` and `$presenter` only when the class declares them, so declare the ones your templates use (here `$presenter` and `$basePath`).

## What happens without the declaration

`undeclared.php` in the same example creates a template from a class that declares nothing and from one that declares only `$appName`:

<<< @/../examples/typed-template/undeclared.php{php}

PHP reports nothing; each template gets only what its class declares:

```text
PlainTemplate: {"appName":false,"settings":false}
PartialTemplate: {"appName":true,"settings":false}
PartialTemplate appName: Acme Shop
```

A template that uses a skipped variable fails where it reads it, as for any variable that was never set (Latte reports an undefined variable). Declare the properties the template uses, or add `#[\AllowDynamicProperties]` to the class to take every registered variable (which gives up the type safety).

::: info Before 4.0
3.x assigned every registered variable to every template, so a typed class without the declaration got the value plus one "Creation of dynamic property ... is deprecated" per variable.
:::

## Types must match

Skipping applies only to properties the class does not declare. A declared property is typed, so the registered value must fit: registering `appName` as an `int` for a `string $appName` property makes `createTemplate()` throw a `TypeError` ("Cannot assign int to property ..."). The library's own code uses `declare(strict_types=1)`, so there is no conversion between scalar types. This also applies to the names Nette's default template declares, such as `string $basePath`.
