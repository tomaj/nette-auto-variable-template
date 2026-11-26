---
title: "Typované šablóny"
description: "Trieda šablóny, ktorá deklaruje zaregistrované premenné, ktorú Nette nájde podľa názvu presentera a ktorá sa používa s {templateType}; a trieda, ktorá ich nedeklaruje, a preto ich nedostane."
---

# Typované šablóny {#typed-templates}

<ExampleSource name="typed-template" />

`HomeTemplate` deklaruje zaregistrované premenné (cez `BaseTemplate`) aj vlastnú premennú stránky `$title`. Nette ju nájde podľa názvu presentera, vytvorí z nej šablónu a zaregistrované premenné skončia v typovaných vlastnostiach. `undeclared.php` ukazuje, že trieda dostane len tie zaregistrované premenné, ktoré deklaruje. Pozrite [Vlastné triedy šablón](/sk/guide/template-classes).

## Konfigurácia {#configuration}

<<< @/../examples/typed-template/config/common.neon{neon}

## Triedy šablón {#template-classes}

<<< @/../examples/typed-template/app/Presenters/BaseTemplate.php{php}

<<< @/../examples/typed-template/app/Presenters/HomeTemplate.php{php}

## Presenter a šablóny {#presenter-and-templates}

<<< @/../examples/typed-template/app/Presenters/HomePresenter.php{php}

<<< @/../examples/typed-template/app/Presenters/templates/@layout.latte{latte}

<<< @/../examples/typed-template/app/Presenters/templates/Home/default.latte{latte}

## Trieda bez deklarácií {#a-class-without-the-declarations}

<<< @/../examples/typed-template/undeclared.php{php}

## Výstup {#output}

`run.php` zavolá `/` a potom spustí `undeclared.php`:

<<< @/../examples/typed-template/expected-output.txt{html}

Stránka sa vykreslí s `App\Presenters\HomeTemplate`. `PlainTemplate` nedostane žiadnu zaregistrovanú premennú a `PartialTemplate` len `$appName`; PHP nehlási žiadnu deprecation.
