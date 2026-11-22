# Change Log
All notable changes to this project will be documented in this file.
Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [Unreleased][unreleased]

### Changed
- **BREAKING CHANGE**: requires PHP 8.4 or newer (`"php": "^8.4"`, was `>= 8.1.0`). Upgrade PHP before updating the package, or stay on 3.x. ([#7](https://github.com/tomaj/nette-auto-variable-template/issues/7))
- Autoloading uses PSR-4 (`Tomaj\Latte\` → `src/`) instead of a classmap. The class name is unchanged. ([#13](https://github.com/tomaj/nette-auto-variable-template/issues/13))
- The Composer dist archive contains only `src/`, `composer.json` and the README, CHANGELOG and UPGRADE files; tool configs and the `.github` folder are no longer installed into `vendor/`. ([#13](https://github.com/tomaj/nette-auto-variable-template/issues/13))

### Added
- GitHub Actions CI: lint on PHP 8.3–8.6 (plus `--prefer-lowest`, nightly 8.7 allowed to fail), PHP-CS-Fixer (PER-CS), PHPStan level max with strict rules, `composer audit`.
- Dependabot for Composer and GitHub Actions.
- Rector (PHP 8.4 set, dead code, code quality, type declarations, early return), PHP-CS-Fixer with `declare_strict_types` over `src` and `tests`, PHPStan with phpstan-phpunit, `checkMissingOverrideMethodAttribute` and `checkUninitializedProperties`; composer scripts `phpunit`, `test`, `coverage`, `infection`, `rector`, `rector-fix` and `check` with descriptions. The `lint` script is gone. ([#9](https://github.com/tomaj/nette-auto-variable-template/issues/9))
- `CONTRIBUTING.md`, `CODE_OF_CONDUCT.md`, `SECURITY.md` (supported versions, private vulnerability reporting, what not to register), `RELEASING.md`, `UPGRADE-4.0.md`, issue and pull request templates. ([#13](https://github.com/tomaj/nette-auto-variable-template/issues/13))

### Fixed
- README no longer links a `LICENSE` file that does not exist; it states the licence declared in `composer.json`. ([#13](https://github.com/tomaj/nette-auto-variable-template/issues/13), [#15](https://github.com/tomaj/nette-auto-variable-template/issues/15))

## [3.0.0] - 2025-12-05

PHP 8.1+ and Nette Application 3.2 / Utils 4.

### Changed
- **BREAKING CHANGE**: requires PHP >= 8.1, nette/application ^3.2 and nette/utils ^4.0 ([#6](https://github.com/tomaj/nette-auto-variable-template/pull/6)).
- **BREAKING CHANGE**: the factory implements `Nette\Application\UI\TemplateFactory` instead of the deprecated `ITemplateFactory`; the constructor takes a `TemplateFactory` and `createTemplate()` returns `Template` instead of `ITemplate` ([#6](https://github.com/tomaj/nette-auto-variable-template/pull/6)). Subclasses overriding `createTemplate()` must update the signature.
- `createTemplate(?Control $control = null, ?string $class = null)` uses explicit nullable types, so it no longer triggers the PHP 8.4 implicit-nullable deprecation.
- Typed properties for the internal state.
- README rewritten with configuration, template usage and `onCreate` examples.

## [2.2.0] - 2021-11-12

### Added
- `getVariables(): array` returns all registered variables ([#5](https://github.com/tomaj/nette-auto-variable-template/pull/5)).

## [2.1.0] - 2021-03-30

### Added
- `createTemplate()` accepts an optional second argument `$class` (the template class name) and passes it to the inner template factory, so `createTemplate($control, MyTemplate::class)` works through the decorator ([#4](https://github.com/tomaj/nette-auto-variable-template/pull/4)).

### Changed
- README: the `config.neon` example uses `factory:` and `autowired: no` (Nette 3 syntax).

## [2.0.0] - 2019-04-18

Nette 3 support and a new `onCreate` event.

### Added
- `onCreate` event (`$factory->onCreate[] = function (ITemplate $template) {...}`), called after the registered variables are assigned to a new template ([#3](https://github.com/tomaj/nette-auto-variable-template/pull/3)).

### Changed
- **BREAKING CHANGE**: requires nette/application ^3.0 and nette/utils ^3.0; Nette 2.x is no longer supported ([#1](https://github.com/tomaj/nette-auto-variable-template/pull/1), [#2](https://github.com/tomaj/nette-auto-variable-template/pull/2)).
- **BREAKING CHANGE**: requires PHP >= 7.1.
- **BREAKING CHANGE**: native types added: `createTemplate()` returns `ITemplate`, `registerVariable(string $key, $variable): self`. Subclasses overriding these methods must add the same types.
- License identifier in composer.json changed from `LGPL` to the SPDX identifier `LGPL-2.0-or-later`.

## [1.0] - 2015-08-28

### Added
- Initial release. `Tomaj\Latte\AutoVariableTemplateFactory`: a Nette `ITemplateFactory` decorator that wraps the existing template factory and assigns registered variables to every template it creates (presenter and component templates).
- `registerVariable($key, $value)` (fluent) to register variables, typically from `config.neon` `setup`.
- Requires PHP >= 5.4 and nette/application ~2.3.

[unreleased]: https://github.com/tomaj/nette-auto-variable-template/compare/3.0.0...HEAD
[3.0.0]: https://github.com/tomaj/nette-auto-variable-template/compare/2.2.0...3.0.0
[2.2.0]: https://github.com/tomaj/nette-auto-variable-template/compare/2.1.0...2.2.0
[2.1.0]: https://github.com/tomaj/nette-auto-variable-template/compare/2.0.0...2.1.0
[2.0.0]: https://github.com/tomaj/nette-auto-variable-template/compare/1.0...2.0.0
[1.0]: https://github.com/tomaj/nette-auto-variable-template/releases/tag/1.0
