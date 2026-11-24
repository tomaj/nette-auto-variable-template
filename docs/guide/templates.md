---
title: Presenters and components
description: Registered variables in presenter templates, layouts, component templates and templates you create yourself, for example for e-mails.
---

# Presenters and components

Every template that goes through the autowired template factory gets the registered variables. In a Nette application that is every `$this->template`.

## Presenter templates and layouts

A presenter's template is created when the presenter first touches `$this->template`; the layout (`@layout.latte`) is rendered with the same template object, so the layout and the page template see the same variables. See [Getting started](/guide/getting-started) and the [hello world example](/examples/hello-world).

The presenter can still change a registered value for its own template, because it assigns after the template was created:

```php
$template->appName = 'Admin – ' . $template->appName;
echo $template->appName;
```

<!-- snippet: output -->
```html
Admin – My Application
```

## Component templates

`Nette\Application\UI\Control::createTemplate()` asks the presenter's template factory for the template, so components get the same variables without passing them through the constructor. From the [components example](/examples/components):

<<< @/../examples/components/app/Components/CartControl.php{php}

<<< @/../examples/components/app/Components/cart.latte{latte}

`$currency` and `$appName` come from the factory; `$count` and `$total` from the control. In the output, the cart shows `3 items, 59.70 EUR <small>(cart in Acme Shop)</small>`.

This also holds for components from other packages (a data grid, a form renderer with its own template): they get your variables too. Keep that in mind for [what you register](/guide/security).

## Templates you create yourself

For an e-mail or a PDF, ask the template factory for a template instead of creating a Latte engine yourself; the registered variables are already there:

```php
use Nette\Application\UI\TemplateFactory;
use Nette\Bridges\ApplicationLatte\Template;

final class OrderMailer
{
    public function __construct(
        private TemplateFactory $templateFactory,
    ) {}

    public function body(string $customer): string
    {
        $template = $this->templateFactory->createTemplate();
        \assert($template instanceof Template);

        return $template->renderToString(__DIR__ . '/order.latte', ['customer' => $customer]);
    }
}

file_put_contents(__DIR__ . '/order.latte', 'Dear {$customer}, thank you for your order at {$appName}.');
echo new OrderMailer($factory)->body('Jane');
unlink(__DIR__ . '/order.latte');
```

<!-- snippet: output -->
```html
Dear Jane, thank you for your order at My Application.
```

Parameters passed to `render()` or `renderToString()` are assigned to the template object before it renders, so `['appName' => ...]` there replaces the registered value on that template. In a Nette application, `TemplateFactory` is autowired, so the mailer gets the decorated factory from the container. The [without DI example](/examples/without-di) renders an invoice and an e-mail this way.

## Templates that do not get the variables

- Templates rendered directly by a `Latte\Engine` (`$latte->render(...)`), without a template factory.
- Templates created by a template factory that is not the decorated one, for example when a service gets `@latte.templateFactory` by name.
