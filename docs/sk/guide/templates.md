---
title: Presentery a komponenty
description: Zaregistrované premenné v šablónach presenterov, v layoutoch, v šablónach komponentov a v šablónach, ktoré si vytvárate sami, napríklad pre e-maily.
---

# Presentery a komponenty {#presenters-and-components}

Každá šablóna, ktorá prejde autowirovanou továrňou na šablóny, dostane zaregistrované premenné. V Nette aplikácii je to každé `$this->template`.

## Šablóny presenterov a layouty {#presenter-templates-and-layouts}

Šablóna presentera sa vytvorí, keď presenter prvýkrát siahne na `$this->template`; layout (`@layout.latte`) sa vykresľuje s tým istým objektom šablóny, takže layout aj šablóna stránky vidia rovnaké premenné. Pozrite [Začíname](/sk/guide/getting-started) a [príklad hello world](/sk/examples/hello-world).

Presenter môže zaregistrovanú hodnotu pre svoju šablónu stále zmeniť, pretože priraďuje až po jej vytvorení:

```php
$template->appName = 'Admin – ' . $template->appName;
echo $template->appName;
```

<!-- snippet: output -->
```html
Admin – My Application
```

## Šablóny komponentov {#component-templates}

`Nette\Application\UI\Control::createTemplate()` si šablónu vyžiada od továrne na šablóny presentera, takže komponenty dostanú rovnaké premenné bez toho, aby ste ich odovzdávali cez konštruktor. Z [príkladu s komponentmi](/sk/examples/components):

<<< @/../examples/components/app/Components/CartControl.php{php}

<<< @/../examples/components/app/Components/cart.latte{latte}

`$currency` a `$appName` pochádzajú z továrne, `$count` a `$total` z komponentu. Vo výstupe košík ukazuje `3 items, 59.70 EUR <small>(cart in Acme Shop)</small>`.

Platí to aj pre komponenty z iných balíkov (dátový grid, vykresľovač formulárov s vlastnou šablónou): aj tie dostanú vaše premenné. Myslite na to pri tom, [čo registrujete](/sk/guide/security).

## Šablóny, ktoré si vytvárate sami {#templates-you-create-yourself}

Pre e-mail alebo PDF si vyžiadajte šablónu od továrne na šablóny namiesto toho, aby ste si sami vytvárali Latte engine; zaregistrované premenné v nej už budú:

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

Parametre odovzdané do `render()` alebo `renderToString()` sa priradia objektu šablóny pred vykreslením, takže `['appName' => ...]` tam nahradí zaregistrovanú hodnotu v tejto šablóne. V Nette aplikácii je `TemplateFactory` autowirovaná, takže mailer dostane z kontajnera dekorovanú továreň. [Príklad bez DI](/sk/examples/without-di) takto vykresľuje faktúru a e-mail.

## Šablóny, ktoré premenné nedostanú {#templates-that-do-not-get-the-variables}

- Šablóny vykresľované priamo cez `Latte\Engine` (`$latte->render(...)`) bez továrne na šablóny.
- Šablóny vytvorené inou ako dekorovanou továrňou na šablóny, napríklad keď služba dostane `@latte.templateFactory` podľa názvu.
