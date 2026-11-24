---
title: Examples
description: Seven runnable examples of nette-auto-variable-template, tested in CI - a presenter with a layout, components, onCreate, precedence, typed templates, plain PHP without DI and PHPUnit tests.
---

# Examples

Each example is a small project in [`examples/`](https://github.com/tomaj/nette-auto-variable-template/tree/master/examples) with its own `composer.json`, a `run.php` and an `expected-output.txt`. Six of them are Nette applications with a `www/index.php` front controller; `run.php` requests a few URLs from it in a fresh PHP process each and prints the HTML. The [Examples workflow](https://github.com/tomaj/nette-auto-variable-template/actions/workflows/examples.yml) runs every `run.php` against the code of each pull request and fails when the output differs from `expected-output.txt` by a single character. The pages below include the files directly, so the code and the output you read are the tested ones.

<LinkCards :items="[
  { title: 'Hello world', text: 'appName and a settings service in a presenter, its layout and two pages.', link: '/examples/hello-world' },
  { title: 'Components', text: 'A menu and a cart component print the registered variables in their own templates.', link: '/examples/components' },
  { title: 'onCreate', text: 'A handler service adds a money filter and a CSS class computed per template.', link: '/examples/on-create' },
  { title: 'Which value wins', text: 'Nette defaults, registered variables, onCreate and the presenter set the same names.', link: '/examples/precedence' },
  { title: 'Typed templates', text: 'Registered variables declared on a template class, and what happens without it.', link: '/examples/typed-template' },
  { title: 'Without Nette DI', text: 'The factory wired by hand in a script: an invoice and an e-mail.', link: '/examples/without-di' },
  { title: 'Testing', text: 'PHPUnit tests for the container, the variables and a rendered presenter.', link: '/examples/testing' },
]" />

## Running an example

```bash
git clone https://github.com/tomaj/nette-auto-variable-template.git
cd nette-auto-variable-template/examples/hello-world
composer install
php run.php                      # prints the pages, the same as expected-output.txt
php -S localhost:8000 -t www     # or open http://localhost:8000/ in a browser
```

The examples need PHP 8.4 and Composer, nothing else: no database, no web server besides PHP's built-in one, no network access after `composer install`.

Until 4.0.0 is tagged, every example's `composer.json` requires `tomaj/nette-auto-variable-template: dev-master` from the repository itself (a Composer [path repository](https://getcomposer.org/doc/05-repositories.md#path) pointing at `../..`), so `composer install` uses the code of your checkout. In your own project, require the released version instead:

```bash
composer require tomaj/nette-auto-variable-template
```

`examples/ci/run.sh <name>` is what CI runs: it installs the example, checks that `vendor/tomaj/nette-auto-variable-template` is the checkout, runs `run.php` and diffs the output with `expected-output.txt`.
