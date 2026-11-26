---
title: "Typed templates"
description: "A template class that declares the registered variables, found by Nette from the presenter name and used with {templateType}; and a class that does not declare them, which does not get them."
---

# Typed templates

<ExampleSource name="typed-template" />

`HomeTemplate` declares the registered variables (through `BaseTemplate`) and the page's own `$title`. Nette finds it by the presenter's name, creates the template from it, and the registered variables land in typed properties. `undeclared.php` shows that a class gets only the registered variables it declares. See [Custom template classes](/guide/template-classes).

## Configuration

<<< @/../examples/typed-template/config/common.neon{neon}

## Template classes

<<< @/../examples/typed-template/app/Presenters/BaseTemplate.php{php}

<<< @/../examples/typed-template/app/Presenters/HomeTemplate.php{php}

## Presenter and templates

<<< @/../examples/typed-template/app/Presenters/HomePresenter.php{php}

<<< @/../examples/typed-template/app/Presenters/templates/@layout.latte{latte}

<<< @/../examples/typed-template/app/Presenters/templates/Home/default.latte{latte}

## A class without the declarations

<<< @/../examples/typed-template/undeclared.php{php}

## Output

`run.php` requests `/` and then runs `undeclared.php`:

<<< @/../examples/typed-template/expected-output.txt{html}

The page renders with `App\Presenters\HomeTemplate`. `PlainTemplate` gets no registered variable and `PartialTemplate` only `$appName`; PHP reports no deprecation.
