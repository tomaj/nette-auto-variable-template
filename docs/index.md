---
layout: home
title: Nette Auto Variable Template
titleTemplate: Variables in every Nette template

hero:
  name: Nette Auto Variable Template
  text: Register once, use in every template
  tagline: A Nette template factory that assigns your variables to every Latte template, in presenters and components alike.
  image:
    src: /logo.svg
    alt: Nette Auto Variable Template
  actions:
    - theme: brand
      text: Get started
      link: /guide/getting-started
    - theme: alt
      text: API reference
      link: /api/reference
    - theme: alt
      text: GitHub
      link: https://github.com/tomaj/nette-auto-variable-template

features:
  - title: Configured in config.neon
    details: Wrap Nette's template factory and register variables and services with setup lines. No base presenter, no traits.
    link: /guide/getting-started#configuration
  - title: Every template
    details: Presenter templates, component templates and templates you create yourself get the same variables.
    link: /guide/getting-started#using-the-variables
  - title: onCreate event
    details: Run your own code for every new template, after the variables are assigned, for filters or late values.
    link: /guide/getting-started#running-code-for-every-template
  - title: One small class
    details: A decorator around Nette\Application\UI\TemplateFactory with four public methods and one event.
    link: /api/reference
---

## Quick example

```neon
services:
	latte.templateFactory:
		autowired: no

	templateFactory:
		factory: Tomaj\Latte\AutoVariableTemplateFactory(@latte.templateFactory)
		setup:
			- registerVariable('appName', 'My Application')
```

```latte
<title>{$appName}</title>
```

Install it with `composer require tomaj/nette-auto-variable-template` (PHP 8.4+, nette/application 3.2+) and read [Getting started](/guide/getting-started).
