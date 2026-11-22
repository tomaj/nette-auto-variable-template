# Upgrading from 3.x to 4.0

4.0 keeps the configuration of 3.x: the `config.neon` service definition, `registerVariable()`, `getVariables()` and `onCreate` work as before. The breaking changes are about the platform and the class itself.

## PHP 8.4 or newer

4.0 requires PHP 8.4 (`"php": "^8.4"`; 3.x allowed PHP 8.1+). Stay on 3.x until you run a supported PHP version.

Before (`composer.json`):

```json
"require": {
    "tomaj/nette-auto-variable-template": "^3.0"
}
```

After:

```json
"require": {
    "php": "^8.4",
    "tomaj/nette-auto-variable-template": "^4.0"
}
```

## The factory is final

`Tomaj\Latte\AutoVariableTemplateFactory` is `final`. Configure it instead of extending it: register variables with `registerVariable()` and run code for every template with `onCreate`.

Before:

<!-- snippet: skip -->
```php
use Nette\Application\UI\Control;
use Nette\Application\UI\Template;
use Tomaj\Latte\AutoVariableTemplateFactory;

class MyTemplateFactory extends AutoVariableTemplateFactory
{
    public function createTemplate(?Control $control = null, ?string $class = null): Template
    {
        $template = parent::createTemplate($control, $class);
        $template->addFilter('money', fn (float $amount): string => number_format($amount, 2) . ' €');

        return $template;
    }
}
```

After:

```php
use Nette\Application\UI\Template;
use Nette\Bridges\ApplicationLatte\Template as LatteTemplate;
use Tomaj\Latte\AutoVariableTemplateFactory;

$factory = new AutoVariableTemplateFactory($latteTemplateFactory);
$factory->onCreate[] = function (Template $template): void {
    if ($template instanceof LatteTemplate) {
        $template->addFilter('money', fn (float $amount): string => number_format($amount, 2) . ' €');
    }
};
```

In `config.neon` the handler is added with a setup line:

```neon
services:
	templateFactory:
		factory: Tomaj\Latte\AutoVariableTemplateFactory(@latte.templateFactory)
		setup:
			- '$onCreate[]' = [@App\Templates\TemplateFilters, 'register']
```

If you need to change what `createTemplate()` returns, decorate the factory instead: implement `Nette\Application\UI\TemplateFactory`, call `AutoVariableTemplateFactory::createTemplate()` from your class and register your class as the autowired template factory.

## No more `Nette\SmartObject`

The class no longer uses `Nette\SmartObject`:

- `onCreate` is a plain typed property, `public array $onCreate`. Adding handlers is unchanged: `$factory->onCreate[] = $callback` in PHP or `'$onCreate[]' = ...` in `config.neon`. Assigning something that is not an array now throws a `TypeError`.
- The magic method `$factory->onCreate($template)` that fired the handlers is gone; `createTemplate()` calls them itself. If your code fired the event by hand, call the handlers directly.

Before:

<!-- snippet: skip -->
```php
$factory->onCreate($template);
```

After:

```php
foreach ($factory->onCreate as $handler) {
    $handler($template);
}
```

- Reading or writing a property the class does not declare no longer throws `Nette\MemberAccessException`; PHP reports it as for any other class.

`nette/utils` is no longer required directly. If your application uses `Nette\Utils` itself, require `nette/utils` in your own `composer.json` (nette/application still installs it).
