# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

### Changed
- **BC break:** minimum PHP version raised from 8.1 to 8.3 (`"php": "^8.3"`).

### Added
- GitHub Actions CI: lint on PHP 8.3–8.6 (plus prefer-lowest, nightly 8.7 allowed to fail), php-cs-fixer (PER-CS), PHPStan level max with strict rules, composer audit.
- Dependabot for Composer and GitHub Actions.
