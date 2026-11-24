---
title: "Komponenty"
description: "Zaregistrované premenné v šablónach Nette komponentov: komponenty menu a košík vypisujú názov aplikácie a menu bez toho, aby ich niekto odovzdával."
---

# Komponenty {#components}

<ExampleSource name="components" />

Dva komponenty, menu a košík, vykresľujú vlastné šablóny. Ani jeden nedostáva `$appName` ani `$currency` cez konštruktor: továreň na šablóny presentera, ktorou je `AutoVariableTemplateFactory`, ich pridá do šablóny každého komponentu.

## Konfigurácia {#configuration}

<<< @/../examples/components/config/common.neon{neon}

## Komponenty {#components-1}

<<< @/../examples/components/app/Components/MenuControl.php{php}

<<< @/../examples/components/app/Components/menu.latte{latte}

<<< @/../examples/components/app/Components/CartControl.php{php}

<<< @/../examples/components/app/Components/cart.latte{latte}

`{$control->getName()}` vypíše `cart`: továreň z Nette nastaví `$control` na komponent a zaregistrované premenné sa pridajú vedľa neho.

## Presenter a šablóny {#presenter-and-templates}

<<< @/../examples/components/app/Presenters/HomePresenter.php{php}

<<< @/../examples/components/app/Presenters/templates/@layout.latte{latte}

<<< @/../examples/components/app/Presenters/templates/Home/default.latte{latte}

<<< @/../examples/components/app/Presenters/templates/Home/products.latte{latte}

## Výstup {#output}

<<< @/../examples/components/expected-output.txt{html}
