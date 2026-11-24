---
title: "Components"
description: "Registered variables in the templates of Nette components: a menu and a cart control print the app name and the currency without passing them."
---

# Components

<ExampleSource name="components" />

Two components, a menu and a cart, render their own templates. Neither gets `$appName` or `$currency` through its constructor: the presenter's template factory, which is `AutoVariableTemplateFactory`, adds them to every component template.

## Configuration

<<< @/../examples/components/config/common.neon{neon}

## Components

<<< @/../examples/components/app/Components/MenuControl.php{php}

<<< @/../examples/components/app/Components/menu.latte{latte}

<<< @/../examples/components/app/Components/CartControl.php{php}

<<< @/../examples/components/app/Components/cart.latte{latte}

`{$control->getName()}` prints `cart`: Nette's factory sets `$control` to the component, and the registered variables are added next to it.

## Presenter and templates

<<< @/../examples/components/app/Presenters/HomePresenter.php{php}

<<< @/../examples/components/app/Presenters/templates/@layout.latte{latte}

<<< @/../examples/components/app/Presenters/templates/Home/default.latte{latte}

<<< @/../examples/components/app/Presenters/templates/Home/products.latte{latte}

## Output

<<< @/../examples/components/expected-output.txt{html}
