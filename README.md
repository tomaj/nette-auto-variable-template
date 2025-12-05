Nette Auto Variable Template Factory
=====================================

[![Latest Stable Version](https://img.shields.io/packagist/v/tomaj/nette-auto-variable-template.svg)](https://packagist.org/packages/tomaj/nette-auto-variable-template)
[![License](https://img.shields.io/badge/license-LGPL--2.0--or--later-blue.svg)](LICENSE)

A lightweight Nette Framework library that automatically injects variables into all Latte templates, including component templates. This is useful when you need certain variables (such as application settings, user preferences, or global configuration) to be available across all templates without manually passing them from each presenter or component.

## Requirements

- PHP >= 8.1
- Nette Application ^3.2
- Nette Utils ^4.0

## Installation

Install the package via Composer:

```bash
composer require tomaj/nette-auto-variable-template
```

## Usage

### Basic Configuration

Register the custom template factory in your `config.neon` file. The library wraps the default Latte template factory and automatically assigns your registered variables to every template:

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

### Using Variables in Templates

Once configured, the registered variables are automatically available in all your Latte templates:

```latte
<h1>{$appName}</h1>
<p>Version: {$settings->getVersion()}</p>
```

### Registering Services

You can register any value as a template variable, including services from the DI container:

```neon
services:
    templateFactory:
        factory: Tomaj\Latte\AutoVariableTemplateFactory(@latte.templateFactory)
        setup:
            - registerVariable('user', @security.user)
            - registerVariable('translator', @translator)
            - registerVariable('config', @App\Model\ConfigService)
```

### onCreate Event

The factory also provides an `onCreate` event that fires whenever a template is created, allowing you to perform additional template customization:

```php
$templateFactory->onCreate[] = function (Template $template) {
    // Custom template initialization
};
```

## How It Works

The `AutoVariableTemplateFactory` implements `Nette\Application\UI\TemplateFactory` and acts as a decorator around the default template factory. When a template is created:

1. The inner (original) template factory creates the template
2. All registered variables are automatically assigned to the template
3. The `onCreate` event is triggered
4. The template is returned ready to use

This approach ensures that variables are available in presenter templates, component templates, and any other templates created through the factory.

## License

This library is licensed under the LGPL-2.0-or-later license. See the [LICENSE](LICENSE) file for details.
