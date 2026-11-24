---
title: Vlastné triedy šablón
description: Používajte zaregistrované premenné s typovo bezpečnými triedami šablón z Nette a {templateType} a vyhnite sa deprecation dynamických vlastností.
---

# Vlastné triedy šablón {#custom-template-classes}

Predvolená trieda šablóny v Nette, `Nette\Bridges\ApplicationLatte\DefaultTemplate`, má `#[\AllowDynamicProperties]`, takže prijme akúkoľvek premennú vrátane zaregistrovaných. [Typovo bezpečná trieda šablóny](https://doc.nette.org/en/application/templates#toc-type-safe-templates), ktorá dedí od `Nette\Bridges\ApplicationLatte\Template`, nie: od PHP 8.2 je priradenie vlastnosti, ktorú trieda nedeklaruje, zastarané (deprecated) a `createTemplate()` priraďuje každú zaregistrovanú premennú.

## Deklarujte zaregistrované premenné {#declare-the-registered-variables}

Zaregistrované premenné dajte do základnej triedy a dedte od nej. Z [príkladu s typovanými šablónami](/sk/examples/typed-template):

<<< @/../examples/typed-template/app/Presenters/BaseTemplate.php{php}

<<< @/../examples/typed-template/app/Presenters/HomeTemplate.php{php}

Nette použije `HomeTemplate` pre `HomePresenter` podľa jej názvu (`HomePresenter` → `HomeTemplate`, alebo `HomeDefaultTemplate` pre akciu `default`); komponenty sa riadia rovnakým pravidlom (`MenuControl` → `MenuTemplate`). Značka `{templateType}` dá typy Latte aj vášmu IDE:

<<< @/../examples/typed-template/app/Presenters/templates/Home/default.latte{latte}

Továreň z Nette nastavuje `$user`, `$baseUrl`, `$basePath`, `$flashes`, `$control` a `$presenter` len vtedy, keď ich trieda deklaruje, takže deklarujte tie, ktoré vaše šablóny používajú (tu `$presenter` a `$basePath`).

## Čo sa stane bez deklarácie {#what-happens-without-the-declaration}

`undeclared.php` v tom istom príklade vytvorí šablónu z triedy, ktorá nedeklaruje nič:

<<< @/../examples/typed-template/undeclared.php{php}

Premenné sa stále priradia, ale PHP každú z nich nahlási:

```text
Deprecated: Creation of dynamic property PlainTemplate::$appName is deprecated
Deprecated: Creation of dynamic property PlainTemplate::$settings is deprecated
appName is still assigned: Acme Shop
```

Deklarujte vlastnosti (alebo pridajte triede `#[\AllowDynamicProperties]`, čím sa vzdáte typovej bezpečnosti). Budúca verzia PHP môže z tejto deprecation urobiť chybu.

## Typy sa musia zhodovať {#types-must-match}

Deklarovaná vlastnosť je typovaná, takže zaregistrovaná hodnota jej musí zodpovedať: ak zaregistrujete `appName` ako `int` pre vlastnosť `string $appName`, `createTemplate()` vyhodí `TypeError` ("Cannot assign int to property ..."). Kód knižnice používa `declare(strict_types=1)`, takže medzi skalárnymi typmi neprebieha žiadna konverzia. To platí aj pre názvy, ktoré deklaruje predvolená šablóna z Nette, napríklad `string $basePath`.
