---
title: Príklady
description: Sedem spustiteľných príkladov nette-auto-variable-template testovaných v CI - presenter s layoutom, komponenty, onCreate, priorita hodnôt, typované šablóny, čisté PHP bez DI a PHPUnit testy.
---

# Príklady {#examples}

Každý príklad je malý projekt v [`examples/`](https://github.com/tomaj/nette-auto-variable-template/tree/master/examples) s vlastným `composer.json`, súborom `run.php` a `expected-output.txt`. Päť z nich sú Nette aplikácie s front controllerom `www/index.php`; ich `run.php` spustí aplikáciu vo vstavanom webovom serveri PHP (`php -S`), zavolá niekoľko URL a vypíše HTML, pričom každá chyba alebo deprecation z PHP sa zobrazí priamo na stránke. `without-di` je obyčajný skript a `testing` spúšťa PHPUnit. [Workflow Examples](https://github.com/tomaj/nette-auto-variable-template/actions/workflows/examples.yml) spustí každý `run.php` proti kódu každého pull requestu a zlyhá, keď sa výstup od `expected-output.txt` líši čo i len v jednom znaku. Stránky nižšie súbory priamo načítavajú, takže kód aj výstup, ktorý čítate, sú tie otestované.

<LinkCards :items="[
  { title: 'Hello world', text: 'appName a služba s nastaveniami v presenteri, jeho layoute a dvoch stránkach.', link: '/sk/examples/hello-world' },
  { title: 'Komponenty', text: 'Komponenty menu a košík vypisujú zaregistrované premenné vo vlastných šablónach.', link: '/sk/examples/components' },
  { title: 'onCreate', text: 'Služba s handlerom pridá filter money a CSS triedu počítanú pre každú šablónu.', link: '/sk/examples/on-create' },
  { title: 'Ktorá hodnota vyhrá', text: 'Predvolené hodnoty Nette, zaregistrované premenné, onCreate a presenter nastavujú rovnaké názvy.', link: '/sk/examples/precedence' },
  { title: 'Typované šablóny', text: 'Zaregistrované premenné deklarované v triede šablóny a čo sa stane bez nej.', link: '/sk/examples/typed-template' },
  { title: 'Bez Nette DI', text: 'Továreň zapojená ručne v skripte: faktúra a e-mail.', link: '/sk/examples/without-di' },
  { title: 'Testovanie', text: 'PHPUnit testy pre kontajner, premenné a vykreslený presenter.', link: '/sk/examples/testing' },
]" />

## Spustenie príkladu {#running-an-example}

```bash
git clone https://github.com/tomaj/nette-auto-variable-template.git
cd nette-auto-variable-template/examples/hello-world
composer install
php run.php                      # prints the pages, the same as expected-output.txt
php -S localhost:8000 -t www     # or open http://localhost:8000/ in a browser
```

Príklady potrebujú len PHP 8.4 a Composer, nič iné: žiadnu databázu, žiadny webový server okrem vstavaného v PHP a po `composer install` ani prístup k sieti.

Kým nebude otagovaná verzia 4.0.0, `composer.json` každého príkladu vyžaduje `tomaj/nette-auto-variable-template: dev-master` priamo z repozitára (Composer [path repository](https://getcomposer.org/doc/05-repositories.md#path) smerujúci na `../..`), takže `composer install` použije kód vášho checkoutu. Vo vlastnom projekte namiesto toho vyžadujte vydanú verziu:

```bash
composer require tomaj/nette-auto-variable-template
```

`examples/ci/run.sh <name>` je to, čo spúšťa CI: nainštaluje príklad, overí, že `vendor/tomaj/nette-auto-variable-template` je váš checkout, spustí `run.php` a porovná výstup s `expected-output.txt`.
