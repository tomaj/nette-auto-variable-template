---
title: "Hello world"
description: "Nette aplikácia, ktorej layout a stránky vypisujú zaregistrovaný názov aplikácie a službu s nastaveniami bez toho, aby ich odovzdával presenter."
---

# Hello world

<ExampleSource name="hello-world" />

Najmenšia úplná aplikácia: jeden presenter s dvoma stránkami a layoutom. `config/common.neon` zaregistruje `appName` (reťazec) a `settings` (službu); šablóny vypisujú obe a presenter nastavuje len titulok stránky.

## Konfigurácia {#configuration}

<<< @/../examples/hello-world/config/common.neon{neon}

`Settings` je nemenný value object, vytvorený ako služba s pomenovanými argumentmi:

<<< @/../examples/hello-world/app/Model/Settings.php{php}

## Presenter

<<< @/../examples/hello-world/app/Presenters/HomePresenter.php{php}

## Šablóny {#templates}

<<< @/../examples/hello-world/app/Presenters/templates/@layout.latte{latte}

<<< @/../examples/hello-world/app/Presenters/templates/Home/default.latte{latte}

<<< @/../examples/hello-world/app/Presenters/templates/Home/about.latte{latte}

## Bootstrap a front controller {#bootstrap-and-front-controller}

<<< @/../examples/hello-world/app/Bootstrap.php{php}

<<< @/../examples/hello-world/www/index.php{php}

## Výstup {#output}

`php run.php` zavolá `/` a `/home/about`:

<<< @/../examples/hello-world/run.php{php}

<<< @/../examples/hello-world/expected-output.txt{html}

Obe stránky aj layout vypisujú `Acme Shop` a `4.0.0`, hoci `HomePresenter` ich nikde nespomína.
