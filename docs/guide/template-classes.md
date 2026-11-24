---
title: Custom template classes
description: Use registered variables with type-safe Nette template classes and {templateType}, and avoid the dynamic property deprecation.
---

# Custom template classes

Nette's default template class, `Nette\Bridges\ApplicationLatte\DefaultTemplate`, has `#[\AllowDynamicProperties]`, so it accepts any variable, including the registered ones. A [type-safe template class](https://doc.nette.org/en/application/templates#toc-type-safe-templates) that extends `Nette\Bridges\ApplicationLatte\Template` does not: since PHP 8.2, assigning a property the class does not declare is deprecated, and `createTemplate()` assigns every registered variable.

## Declare the registered variables

Put the registered variables on a base class and extend it. From the [typed template example](/examples/typed-template):

<<< @/../examples/typed-template/app/Presenters/BaseTemplate.php{php}

<<< @/../examples/typed-template/app/Presenters/HomeTemplate.php{php}

Nette uses `HomeTemplate` for `HomePresenter` because of its name (`HomePresenter` → `HomeTemplate`, or `HomeDefaultTemplate` for the `default` action); components follow the same rule (`MenuControl` → `MenuTemplate`). The `{templateType}` tag gives Latte and your IDE the types:

<<< @/../examples/typed-template/app/Presenters/templates/Home/default.latte{latte}

Nette's factory sets `$user`, `$baseUrl`, `$basePath`, `$flashes`, `$control` and `$presenter` only when the class declares them, so declare the ones your templates use (here `$presenter` and `$basePath`).

## What happens without the declaration

`undeclared.php` in the same example creates a template from a class that declares nothing:

<<< @/../examples/typed-template/undeclared.php{php}

The variables are still assigned, but PHP reports each of them:

```text
Deprecated: Creation of dynamic property PlainTemplate::$appName is deprecated
Deprecated: Creation of dynamic property PlainTemplate::$settings is deprecated
appName is still assigned: Acme Shop
```

Declare the properties (or add `#[\AllowDynamicProperties]` to the class, which gives up the type safety). A future PHP version may turn the deprecation into an error.

## Types must match

A declared property is typed, so the registered value must fit: registering `appName` as an `int` for a `string $appName` property makes `createTemplate()` throw a `TypeError` ("Cannot assign int to property ..."). The library's own code uses `declare(strict_types=1)`, so there is no conversion between scalar types. This also applies to the names Nette's default template declares, such as `string $basePath`.
