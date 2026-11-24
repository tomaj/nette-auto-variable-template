---
title: "onCreate: filters and per-template values"
description: "An onCreate handler service adds a money filter that uses a registered currency and a CSS class computed from the presenter or component of each template."
---

# onCreate: filters and per-template values

<ExampleSource name="on-create" />

`TemplateSetup::setUp()` runs for every new template, after `appName` and `currency` are assigned. It adds a `money` filter that formats amounts with the registered currency, and a `$cssClass` variable that differs per template: `page-home-default` for the presenter's template, `component-price` for the component's.

## Configuration

<<< @/../examples/on-create/config/common.neon{neon}

## The handler

<<< @/../examples/on-create/app/Templates/TemplateSetup.php{php}

## Presenter and component

<<< @/../examples/on-create/app/Presenters/HomePresenter.php{php}

<<< @/../examples/on-create/app/Components/PriceControl.php{php}

## Templates

<<< @/../examples/on-create/app/Presenters/templates/@layout.latte{latte}

<<< @/../examples/on-create/app/Presenters/templates/Home/default.latte{latte}

<<< @/../examples/on-create/app/Components/price.latte{latte}

## Output

<<< @/../examples/on-create/expected-output.txt{html}

The `<body>` of the page has `page-home-default` and the component's paragraph has `component-price`: the same handler computed a different value for each template. More in [Running code: onCreate](/guide/on-create).
