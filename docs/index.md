---
layout: home
title: Nette Auto Variable Template
description: Register variables once and use them in every Latte template of your Nette application, in presenters, layouts, components and e-mails.

hero:
  name: Nette Auto Variable Template
  text: Register once, use in every template
  tagline: A template factory for Nette that assigns your variables to every Latte template, in presenters, layouts, components and templates you create yourself. Configured in config.neon, no base presenter.
  image:
    light: /illustrations/hero-light.svg
    dark: /illustrations/hero-dark.svg
    alt: One registerVariable line in config.neon goes through AutoVariableTemplateFactory into a layout, a component and an e-mail template, which all print the app name
  actions:
    - theme: brand
      text: Get started
      link: /guide/getting-started
    - theme: alt
      text: Examples
      link: /examples/
    - theme: alt
      text: GitHub
      link: https://github.com/tomaj/nette-auto-variable-template

features:
  - icon:
      light: /icons/config.svg
      dark: /icons/config-dark.svg
      width: 48
      height: 48
    title: Configured in config.neon
    details: Wrap Nette's template factory with two service entries and register literals, parameters or services with setup lines.
    link: /guide/configuration
    linkText: Configuration
  - icon:
      light: /icons/templates.svg
      dark: /icons/templates-dark.svg
      width: 48
      height: 48
    title: Every template
    details: Presenter templates and layouts, component templates and templates you create for e-mails get the same variables.
    link: /guide/templates
    linkText: Presenters and components
  - icon:
      light: /icons/oncreate.svg
      dark: /icons/oncreate-dark.svg
      width: 48
      height: 48
    title: onCreate handlers
    details: Run your own code for every new template, after the variables are assigned, to add filters or values computed per template.
    link: /guide/on-create
    linkText: onCreate
  - icon:
      light: /icons/layers.svg
      dark: /icons/layers-dark.svg
      width: 48
      height: 48
    title: Predictable order
    details: Registered values replace Nette's defaults, onCreate runs after them, and a presenter or component can still override any of them.
    link: /guide/how-it-works
    linkText: How it works
  - icon:
      light: /icons/typed.svg
      dark: /icons/typed-dark.svg
      width: 48
      height: 48
    title: Works with typed templates
    details: Declare the registered variables on your template classes and keep PHPStan and Latte's {templateType} happy.
    link: /guide/template-classes
    linkText: Custom template classes
  - icon:
      light: /icons/cube.svg
      dark: /icons/cube-dark.svg
      width: 48
      height: 48
    title: One small final class
    details: A decorator around Nette\Application\UI\TemplateFactory with three methods and one event. Tested on PHP 8.4 to 8.6.
    link: /api/reference
    linkText: API reference
---

<div class="home-more">

## What it looks like

Register the variables once in `config.neon`:

```neon
services:
	latte.templateFactory:
		autowired: no

	templateFactory:
		factory: Tomaj\Latte\AutoVariableTemplateFactory(@latte.templateFactory)
		setup:
			- registerVariable('appName', 'Acme Shop')
			- registerVariable('settings', @App\Model\Settings)
```

The layout and every page template use them, and the presenter sets only `$title`:

<<< @/../examples/hello-world/app/Presenters/templates/@layout.latte{latte}

`GET /` renders this (an excerpt of the output of [the hello world example](/examples/hello-world), which CI checks on every change):

```html
<title>Welcome | Acme Shop</title>
<header>Acme Shop</header>
<footer>Acme Shop 4.0.0 · <a href="mailto:help@acme.test">help@acme.test</a></footer>
```

## Where to go next

<LinkCards :items="[
  { title: 'Getting started', text: 'Install the package, register it in config.neon and render the first page.', link: '/guide/getting-started' },
  { title: 'How it works', text: 'What happens when a template is created, and which value wins.', link: '/guide/how-it-works' },
  { title: 'Examples', text: 'Seven runnable Nette apps and scripts, tested in CI.', link: '/examples/' },
  { title: 'API reference', text: 'The constructor, createTemplate(), registerVariable(), getVariables() and onCreate.', link: '/api/reference' },
  { title: 'FAQ', text: 'Autowiring errors, Nette\'s own variables, translators, caching and more.', link: '/faq' },
  { title: 'Upgrading to 4.0', text: 'PHP 8.4, the final class and the end of SmartObject.', link: '/upgrade' },
]" />

</div>
