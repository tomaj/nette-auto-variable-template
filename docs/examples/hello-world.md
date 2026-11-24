---
title: "Hello world"
description: "A Nette application whose layout and pages print a registered app name and a settings service, without passing them from the presenter."
---

# Hello world

<ExampleSource name="hello-world" />

The smallest complete application: one presenter with two pages and a layout. `config/common.neon` registers `appName` (a string) and `settings` (a service); the templates print both, and the presenter sets only the page title.

## Configuration

<<< @/../examples/hello-world/config/common.neon{neon}

`Settings` is a read-only value object, created as a service with named arguments:

<<< @/../examples/hello-world/app/Model/Settings.php{php}

## Presenter

<<< @/../examples/hello-world/app/Presenters/HomePresenter.php{php}

## Templates

<<< @/../examples/hello-world/app/Presenters/templates/@layout.latte{latte}

<<< @/../examples/hello-world/app/Presenters/templates/Home/default.latte{latte}

<<< @/../examples/hello-world/app/Presenters/templates/Home/about.latte{latte}

## Bootstrap and front controller

<<< @/../examples/hello-world/app/Bootstrap.php{php}

<<< @/../examples/hello-world/www/index.php{php}

## Output

`php run.php` requests `/` and `/home/about`:

<<< @/../examples/hello-world/run.php{php}

<<< @/../examples/hello-world/expected-output.txt{html}

Both pages and the layout print `Acme Shop` and `4.0.0`, and `HomePresenter` never mentions them.
