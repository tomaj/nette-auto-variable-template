---
title: "Bez Nette DI"
description: "AutoVariableTemplateFactory zapojená ručne v obyčajnom PHP skripte okolo továrne na Latte šablóny z Nette, ktorá vykreslí faktúru a e-mail s rovnakými zaregistrovanými premennými."
---

# Bez Nette DI {#without-nette-di}

<ExampleSource name="without-di" />

Žiadny kontajner, žiadny presenter: skript ručne vytvorí továreň na Latte šablóny z Nette, obalí ju, zaregistruje `appName` a `company`, v `onCreate` pridá filter `iban` a vykreslí dve šablóny. Takto ju použijete v CLI príkaze, vo workeri fronty alebo v teste.

## Skript {#the-script}

<<< @/../examples/without-di/run.php{php}

## Šablóny {#templates}

<<< @/../examples/without-di/templates/invoice.latte{latte}

<<< @/../examples/without-di/templates/email.latte{latte}

## Výstup {#output}

<<< @/../examples/without-di/expected-output.txt{text}

Obe šablóny vypisujú `Acme Shop` a údaje o firme; pre každú šablónu sa odovzdávajú len `$number`, `$issued` a `$name`.
