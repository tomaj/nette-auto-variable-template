# Nette Auto Variable Template

<p align="left">
  <img src="docs/public/logo.svg" alt="Nette Auto Variable Template" width="96" height="96" />
</p>

**Variables registered once, available in every Nette template**

**Documentation: https://tomaj.github.io/nette-auto-variable-template/**

[Getting started](https://tomaj.github.io/nette-auto-variable-template/guide/getting-started) · [Examples](https://tomaj.github.io/nette-auto-variable-template/examples/) · [API reference](https://tomaj.github.io/nette-auto-variable-template/api/reference) · [Changelog](https://tomaj.github.io/nette-auto-variable-template/changelog)

[![Latest Stable Version](https://img.shields.io/packagist/v/tomaj/nette-auto-variable-template.svg)](https://packagist.org/packages/tomaj/nette-auto-variable-template)
[![CI](https://github.com/tomaj/nette-auto-variable-template/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/tomaj/nette-auto-variable-template/actions/workflows/ci.yml?query=branch%3Amaster)
[![Coverage Status](https://coveralls.io/repos/github/tomaj/nette-auto-variable-template/badge.svg?branch=master)](https://coveralls.io/github/tomaj/nette-auto-variable-template?branch=master)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%20max-brightgreen.svg)](https://phpstan.org/)
[![OpenSSF Scorecard](https://api.securityscorecards.dev/projects/github.com/tomaj/nette-auto-variable-template/badge)](https://scorecard.dev/viewer/?uri=github.com/tomaj/nette-auto-variable-template)
[![Documentation](https://img.shields.io/badge/docs-4.x-4f46e5.svg)](https://tomaj.github.io/nette-auto-variable-template/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/packagist/dependency-v/tomaj/nette-auto-variable-template/php.svg)](https://packagist.org/packages/tomaj/nette-auto-variable-template)

## What is Nette Auto Variable Template?

A small [Nette](https://nette.org/) library that assigns the variables you register to every Latte template the application creates: presenter templates, component templates and any other template made by the template factory. Use it for values that every template needs, such as the application name, the current user or a settings service, instead of passing them from each presenter and component.

`Tomaj\Latte\AutoVariableTemplateFactory` decorates Nette's template factory: the original factory creates the template, then the registered variables are assigned and the `onCreate` handlers run.

## Installation

This library requires PHP 8.4 or later and nette/application 3.2 or later.

```bash
composer require tomaj/nette-auto-variable-template
```

## Usage

Register the factory in `config.neon`. It wraps the default Latte template factory, which is no longer autowired:

```neon
services:
	latte.templateFactory:
		autowired: no

	templateFactory:
		factory: Tomaj\Latte\AutoVariableTemplateFactory(@latte.templateFactory)
		setup:
			- registerVariable('appName', 'My Application')
			- registerVariable('settings', @App\Model\SettingsService)
```

Every template can now use the variables:

```latte
<h1>{$appName}</h1>
<p>Version: {$settings->getVersion()}</p>
```

`$factory->onCreate[]` takes callbacks that run for every new template, after the variables are assigned. Read the [documentation](https://tomaj.github.io/nette-auto-variable-template/) for more, and the [examples](examples/): small Nette applications, tested in CI, that show the variables in layouts, components, `onCreate` handlers and typed templates.

## Upgrading

See [UPGRADE-4.0.md](UPGRADE-4.0.md) for upgrading from 3.x and [CHANGELOG.md](CHANGELOG.md) for all changes.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md). Everyone taking part follows the [Code of Conduct](CODE_OF_CONDUCT.md).

## Security

Registered variables reach every template, so never register secrets. Report vulnerabilities in private, as described in [SECURITY.md](SECURITY.md). Do not open a public issue.

## License

The MIT License (MIT). See [LICENSE](LICENSE). Versions up to 3.x were released under `LGPL-2.0-or-later`.
