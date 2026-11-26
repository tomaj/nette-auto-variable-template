---
title: Vlastné triedy šablón
description: Používajte zaregistrované premenné s typovo bezpečnými triedami šablón z Nette a {templateType}; trieda dostane len tie zaregistrované premenné, ktoré deklaruje.
---

# Vlastné triedy šablón {#custom-template-classes}

Predvolená trieda šablóny v Nette, `Nette\Bridges\ApplicationLatte\DefaultTemplate`, má `#[\AllowDynamicProperties]`, takže prijme akúkoľvek premennú vrátane zaregistrovaných. [Typovo bezpečná trieda šablóny](https://doc.nette.org/en/application/templates#toc-type-safe-templates), ktorá dedí od `Nette\Bridges\ApplicationLatte\Template`, nie: od PHP 8.2 je priradenie vlastnosti, ktorú trieda nedeklaruje, zastarané (deprecated). Preto `createTemplate()` priradí zaregistrovanú premennú len vtedy, keď ju trieda šablóny dokáže prijať:

- trieda vlastnosť deklaruje (sama alebo v rodičovskej triede), alebo
- trieda alebo niektorý jej rodič má `#[\AllowDynamicProperties]` (ako `DefaultTemplate`), alebo
- trieda má metódu `__set()`.

Inak sa premenná pre túto šablónu preskočí, bez deprecation aj bez chyby. Či trieda povoľuje dynamické vlastnosti alebo má `__set()`, sa zisťuje raz pre každú triedu šablóny, nie pri každej požiadavke.

## Deklarujte zaregistrované premenné {#declare-the-registered-variables}

Zaregistrované premenné dajte do základnej triedy a dedte od nej. Z [príkladu s typovanými šablónami](/sk/examples/typed-template):

<<< @/../examples/typed-template/app/Presenters/BaseTemplate.php{php}

<<< @/../examples/typed-template/app/Presenters/HomeTemplate.php{php}

Nette použije `HomeTemplate` pre `HomePresenter` podľa jej názvu (`HomePresenter` → `HomeTemplate`, alebo `HomeDefaultTemplate` pre akciu `default`); komponenty sa riadia rovnakým pravidlom (`MenuControl` → `MenuTemplate`). Značka `{templateType}` dá typy Latte aj vášmu IDE:

<<< @/../examples/typed-template/app/Presenters/templates/Home/default.latte{latte}

Továreň z Nette nastavuje `$user`, `$baseUrl`, `$basePath`, `$flashes`, `$control` a `$presenter` len vtedy, keď ich trieda deklaruje, takže deklarujte tie, ktoré vaše šablóny používajú (tu `$presenter` a `$basePath`).

## Čo sa stane bez deklarácie {#what-happens-without-the-declaration}

`undeclared.php` v tom istom príklade vytvorí šablónu z triedy, ktorá nedeklaruje nič, a z triedy, ktorá deklaruje len `$appName`:

<<< @/../examples/typed-template/undeclared.php{php}

PHP nehlási nič; každá šablóna dostane len to, čo jej trieda deklaruje:

```text
PlainTemplate: {"appName":false,"settings":false}
PartialTemplate: {"appName":true,"settings":false}
PartialTemplate appName: Acme Shop
```

Šablóna, ktorá použije preskočenú premennú, zlyhá na mieste, kde ju číta, ako pri každej premennej, ktorá nikdy nebola nastavená (Latte nahlási nedefinovanú premennú). Deklarujte vlastnosti, ktoré šablóna používa, alebo pridajte triede `#[\AllowDynamicProperties]`, aby prijala všetky zaregistrované premenné (čím sa vzdáte typovej bezpečnosti).

::: info Pred verziou 4.0
Verzia 3.x priraďovala každú zaregistrovanú premennú do každej šablóny, takže typovaná trieda bez deklarácie hodnotu dostala a PHP pri každej premennej hlásilo "Creation of dynamic property ... is deprecated".
:::

## Typy sa musia zhodovať {#types-must-match}

Preskakujú sa len vlastnosti, ktoré trieda nedeklaruje. Deklarovaná vlastnosť je typovaná, takže zaregistrovaná hodnota jej musí zodpovedať: ak zaregistrujete `appName` ako `int` pre vlastnosť `string $appName`, `createTemplate()` vyhodí `TypeError` ("Cannot assign int to property ..."). Kód knižnice používa `declare(strict_types=1)`, takže medzi skalárnymi typmi neprebieha žiadna konverzia. To platí aj pre názvy, ktoré deklaruje predvolená šablóna z Nette, napríklad `string $basePath`.
