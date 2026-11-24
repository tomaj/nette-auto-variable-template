---
title: "Testovanie"
description: "PHPUnit testy, ktoré spustia kontajner aplikácie a overia, že továreň na šablóny je dekorovaná, premenné sú zaregistrované a presenter ich vykreslí."
---

# Testovanie {#testing}

<ExampleSource name="testing" />

Skutočná konfigurácia aplikácie spustená v PHPUnit. Tri testy overujú zapojenie (autowirovaná továreň je `AutoVariableTemplateFactory`), zaregistrované hodnoty (`getVariables()`) a výsledok (presenter ich vykreslí). Pozrite [Testovanie integrácie](/sk/guide/testing).

## Konfigurácia {#configuration}

<<< @/../examples/testing/config/common.neon{neon}

`supportEmail` pochádza z parametra, takže testovacie prostredie ho môže prepísať vo vlastnom konfiguračnom súbore.

## Testy {#the-tests}

<<< @/../examples/testing/tests/TemplateVariablesTest.php{php}

<<< @/../examples/testing/phpunit.xml{xml}

`failOnDeprecation` spôsobí, že sada testov zlyhá, keď trieda šablóny nedeklaruje zaregistrovanú premennú ([Vlastné triedy šablón](/sk/guide/template-classes)).

## Výstup {#output}

`run.php` spustí PHPUnit s `--testdox` a vynechá riadky s verziou PHPUnit a časom:

<<< @/../examples/testing/run.php{php}

<<< @/../examples/testing/expected-output.txt{text}
