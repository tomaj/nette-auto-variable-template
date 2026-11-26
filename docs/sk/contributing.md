---
title: Ako prispieť
description: Ako prispieť do nette-auto-variable-template, spustiť kontroly a príklady a upraviť dokumentáciu.
---

# Ako prispieť {#contributing}

Pull requesty sú vítané v [tomaj/nette-auto-variable-template](https://github.com/tomaj/nette-auto-variable-template). Pravidlá projektu sú v [CONTRIBUTING.md](https://github.com/tomaj/nette-auto-variable-template/blob/master/CONTRIBUTING.md) a táto stránka s nimi súhlasí. Pre účasť na projekte platí [kódex správania](https://github.com/tomaj/nette-auto-variable-template/blob/master/CODE_OF_CONDUCT.md). Bezpečnostné zraniteľnosti sa hlásia neverejne, pozrite [Bezpečnosť](/sk/guide/security).

## Zmeny, ktoré vieme zlúčiť {#changes-we-can-merge}

- Dodržiavajte [PER Coding Style](https://www.php-fig.org/per/coding-style/). `composer phpcs` kontroluje `src` a `tests`.
- Ku každej zmene správania pridajte testy.
- Zmeny správania zdokumentujte v `README.md`, na týchto stránkach a v `CHANGELOG.md`.
- [Sémantické verzovanie](https://semver.org/): nekompatibilné zmeny čakajú na ďalšiu hlavnú verziu.
- Jeden pull request na jednu zmenu, z vlastnej vetvy.

## Kontroly {#checks}

```bash
composer install
composer check      # code style, PHPStan, Rector, tests
composer coverage   # HTML and Clover coverage in build/ (Xdebug or PCOV)
composer infection  # mutation testing
```

CI spúšťa testy na PHP 8.4 (aj s najnižšími závislosťami), 8.5 a 8.6 a navyše na nočnom PHP 8.7, ktoré smie zlyhať. Na PHP 8.6 sa kontroluje aj pokrytie príkazov a spúšťa Infection.

## Príklady {#examples}

Každý príklad v [`examples/`](https://github.com/tomaj/nette-auto-variable-template/tree/master/examples) je malý projekt so súbormi `run.php` a `expected-output.txt`, ktorý načítavajú [stránky s príkladmi](/sk/examples/). Spustite ho rovnako ako CI:

```bash
examples/ci/run.sh hello-world
```

Skript nainštaluje príklad proti vášmu checkoutu (Composer path repository), spustí `run.php` a porovná výstup s `expected-output.txt`. Zmena, ktorá mení výstup príkladu, aktualizuje `expected-output.txt` v tom istom pull requeste. Nový príklad potrebuje aj svoj názov v `.github/workflows/examples.yml` a stránku v `docs/examples/` a `docs/sk/examples/`.

## Dokumentácia {#documentation}

Stránka sa zostavuje pomocou [VitePress](https://vitepress.dev/) z adresára `docs/`:

```bash
cd docs
npm ci
npm run docs:dev    # local preview
npm run docs:links  # build and check every link
npm run check:svg   # check every SVG in public/
```

- Každý blok kódu `php` na týchto stránkach, v `README.md` a `UPGRADE-4.0.md` spúšťa `tests/DocsSnippetsTest.php` v sade PHPUnit, každý blok `neon` sa parsuje a každý blok `latte` kompiluje. Blok označený `<!-- snippet: skip -->` sa nekontroluje; blok `html` označený `<!-- snippet: output -->` sa musí zhodovať s výstupom PHP bloku pred ním.
- Slovenské stránky v `docs/sk/` majú rovnaké cesty ako anglické a rovnaké bloky kódu; keď zmeníte anglickú stránku, upravte aj jej slovenskú dvojičku. `python3 scripts/check-locales.py` (beží v CI) zlyhá, keď stránke chýba dvojička alebo sa bloky kódu líšia.
- Kód z príkladov sa načítava cez `<<< @/../examples/<name>/<file>`, nikdy sa nekopíruje.
- Ilustráciu na úvodnej stránke, ikony funkcií a diagramy generuje `python3 scripts/illustrations.py` (svetlé a tmavé varianty). Po zmene loga `npm run images` znova vygeneruje favicony a obrázok pre sociálne siete `public/og.png` (potrebuje Chrome; nastavte `CHROME_PATH`).

## Vydania {#releases}

Správcovia vydávajú nové verzie pushnutím tagu s verziou, ako popisuje [RELEASING.md](https://github.com/tomaj/nette-auto-variable-template/blob/master/RELEASING.md).

## Licencia {#license}

Balík je vydaný pod [licenciou MIT](https://github.com/tomaj/nette-auto-variable-template/blob/master/LICENSE) (verzie do 3.x pod `LGPL-2.0-or-later`). Prispievaním súhlasíte, že vaše príspevky sú licencované pod ňou.
