---
title: Začíname
description: Nainštalujte nette-auto-variable-template, zaregistrujte ho v config.neon a používajte premenné v každej Latte šablóne Nette aplikácie.
---

# Začíname {#getting-started}

Táto stránka prevedie Nette aplikáciu od nuly k layoutu, ktorý na každej stránke vypíše názov aplikácie a službu s nastaveniami bez toho, aby ich musel odovzdávať ktorýkoľvek presenter. Kód pochádza z [príkladu hello world](/sk/examples/hello-world); každý súbor nižšie sa načítava priamo z neho a CI ho spúšťa pri každej zmene.

## Požiadavky {#requirements}

- PHP 8.4 alebo novšie
- [nette/application](https://doc.nette.org/en/application) 3.2 alebo novšie s [Latte](https://latte.nette.org/) šablónami (`latte/latte` 3)
- Nette DI s rozšírením pre Latte, ktoré má každý [nette/web-project](https://github.com/nette/web-project). Bez DI pozrite [Bez Nette DI](#without-nette-di).

## Inštalácia {#installation}

```bash
composer require tomaj/nette-auto-variable-template
```

Balík obsahuje jedinú triedu, `Tomaj\Latte\AutoVariableTemplateFactory`, a nemá vlastnú konfiguráciu.

## 1. Zaregistrujte továreň {#_1-register-the-factory}

Nette registruje svoju továreň na Latte šablóny ako službu `latte.templateFactory` a presentery aj komponenty ju dostávajú cez autowiring. Vypnite jej autowiring a zaregistrujte okolo nej `AutoVariableTemplateFactory`, aby presentery a komponenty namiesto nej dostali dekorovanú továreň:

<<< @/../examples/hello-world/config/common.neon{neon}

- `latte.templateFactory: autowired: no` ponechá továreň z Nette ako službu (dekorátor ju potrebuje), ale vypne jej autowiring. Bez tohto riadku by mal kontajner dve služby typu `Nette\Application\UI\TemplateFactory` a skončil by chybou „Multiple services of type ... found“.
- `templateFactory` je dekorátor. Ako jediný argument dostane továreň z Nette a odteraz je to autowirovaná továreň na šablóny.
- Každý riadok `registerVariable()` v sekcii `setup` pridá jednu premennú: tu literál (`'Acme Shop'`) a službu (`@App\Model\Settings`). Fungujú aj parametre (`%supportEmail%`) a výrazy. Všetky možnosti nájdete v časti [Konfigurácia](/sk/guide/configuration).

Služba s nastaveniami je v tomto príklade obyčajný hodnotový objekt:

<<< @/../examples/hello-world/app/Model/Settings.php{php}

## 2. Použite premenné v šablónach {#_2-use-the-variables-in-templates}

Každá šablóna vytvorená cez továreň tieto premenné má. Layout používa `$appName` a `$settings`:

<<< @/../examples/hello-world/app/Presenters/templates/@layout.latte{latte}

a rovnako aj šablóny stránok:

<<< @/../examples/hello-world/app/Presenters/templates/Home/default.latte{latte}

Presenter nastavuje len to, čo patrí ku konkrétnej stránke:

<<< @/../examples/hello-world/app/Presenters/HomePresenter.php{php}

## 3. Spustite to {#_3-run-it}

```bash
php -S localhost:8000 -t www
```

Po otvorení `http://localhost:8000/` dostanete:

<<< @/../examples/hello-world/expected-output.txt{html}

Súbor vyššie je presne to, čo vypíše `php run.php` v `examples/hello-world`; spustí aplikáciu vo vstavanom PHP serveri ako vyššie a vyžiada si `/` a `/home/about`. CI ho pri každom pull requeste porovná so skutočným výstupom.

## Bez Nette DI {#without-nette-di}

Továreň prijme akúkoľvek implementáciu `Nette\Application\UI\TemplateFactory`. Tu je `$latteTemplateFactory` továreň `Nette\Bridges\ApplicationLatte\TemplateFactory` z Nette (služba `latte.templateFactory`), ktorú môžete vytvoriť aj ručne, pozrite [príklad bez DI](/sk/examples/without-di):

```php
use Tomaj\Latte\AutoVariableTemplateFactory;

$factory = new AutoVariableTemplateFactory($latteTemplateFactory);
$factory->registerVariable('appName', 'My Application')
    ->registerVariable('year', 2026);

$template = $factory->createTemplate();
echo $template->appName, ' ', $template->year;
```

<!-- snippet: output -->
```html
My Application 2026
```

## Ďalšie kroky {#next-steps}

- [Ako to funguje](/sk/guide/how-it-works): poradie, v akom hodnoty nastavujú predvolené premenné Nette, zaregistrované premenné, handlery `onCreate` a presentery
- [Konfigurácia](/sk/guide/configuration): všetky spôsoby, ako zaregistrovať premennú v `config.neon`
- [Presentery a komponenty](/sk/guide/templates): layouty, šablóny komponentov a šablóny pre e-maily
- [Spúšťanie kódu: onCreate](/sk/guide/on-create): filtre, funkcie a hodnoty počítané pre každú šablónu
- [Príklady](/sk/examples/): sedem spustiteľných projektov
