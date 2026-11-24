---
title: "onCreate: filtre a hodnoty pre každú šablónu"
description: "Služba s handlerom onCreate pridá filter money, ktorý používa zaregistrovanú menu, a CSS triedu vypočítanú z presentera alebo komponentu každej šablóny."
---

# onCreate: filtre a hodnoty pre každú šablónu {#oncreate-filters-and-per-template-values}

<ExampleSource name="on-create" />

`TemplateSetup::setUp()` sa spustí pre každú novú šablónu, keď už sú priradené `appName` a `currency`. Pridá filter `money`, ktorý formátuje sumy so zaregistrovanou menou, a premennú `$cssClass`, ktorá je v každej šablóne iná: `page-home-default` pre šablónu presentera, `component-price` pre šablónu komponentu.

## Konfigurácia {#configuration}

<<< @/../examples/on-create/config/common.neon{neon}

## Handler {#the-handler}

<<< @/../examples/on-create/app/Templates/TemplateSetup.php{php}

## Presenter a komponent {#presenter-and-component}

<<< @/../examples/on-create/app/Presenters/HomePresenter.php{php}

<<< @/../examples/on-create/app/Components/PriceControl.php{php}

## Šablóny {#templates}

<<< @/../examples/on-create/app/Presenters/templates/@layout.latte{latte}

<<< @/../examples/on-create/app/Presenters/templates/Home/default.latte{latte}

<<< @/../examples/on-create/app/Components/price.latte{latte}

## Výstup {#output}

<<< @/../examples/on-create/expected-output.txt{html}

`<body>` stránky má `page-home-default` a odsek komponentu má `component-price`: ten istý handler vypočítal pre každú šablónu inú hodnotu. Viac v časti [Spúšťanie kódu: onCreate](/sk/guide/on-create).
