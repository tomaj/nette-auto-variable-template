---
title: "Lenivá služba: vytvorí sa, až keď sa použije"
description: "Zaregistrovaná služba ExchangeRates označená lazy: true. Jej konštruktor sa nespustí na stránke, ktorá premennú nevypíše, a spustí sa raz na stránke, ktorá ju vypíše."
---

# Lenivá služba: vytvorí sa, až keď sa použije {#lazy-service-constructed-only-when-used}

<ExampleSource name="lazy-service" />

Každá šablóna dostane `$rates`, službu `ExchangeRates`, ktorej konštruktor zastupuje drahý konštruktor: pri každom spustení zapíše riadok do `temp/exchange-rates.log`. Služba je označená `lazy: true` (PHP 8.4, nette/di 3.2.4+), takže továreň šablón zaregistruje lazy ghost a konštruktor sa spustí až vtedy, keď šablóna prvýkrát zavolá metódu, ktorá číta stav objektu. `run.php` po každej stránke vypíše, koľkokrát sa konštruktor zatiaľ spustil.

## Konfigurácia {#configuration}

<<< @/../examples/lazy-service/config/common.neon{neon}

## Služba {#the-service}

<<< @/../examples/lazy-service/app/Model/ExchangeRates.php{php}

## Šablóny {#templates}

<<< @/../examples/lazy-service/app/Presenters/templates/@layout.latte{latte}

<<< @/../examples/lazy-service/app/Presenters/templates/Home/default.latte{latte}

<<< @/../examples/lazy-service/app/Presenters/templates/Home/rates.latte{latte}

## Výstup {#output}

<<< @/../examples/lazy-service/expected-output.txt{html}

Domovská stránka sa vykreslí s priradeným `$rates` a konštruktor sa zatiaľ spustil 0-krát. Stránka s kurzami zavolá `$rates->rate()`, čo konštruktor spustí raz. S `lazy: false` je počet 1 už po domovskej stránke. Viac v časti [Drahé služby: `lazy: true`](/sk/guide/configuration#expensive-services-lazy-true).
