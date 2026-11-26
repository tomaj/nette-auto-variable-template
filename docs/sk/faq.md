---
title: Časté otázky
description: Časté otázky o nette-auto-variable-template - chyby autowiringu, vlastné premenné Nette, translatory, výkon, typované šablóny a ďalšie.
---

# Časté otázky {#faq}

## Kontajner zlyhá s chybou "Multiple services of type Nette\Application\UI\TemplateFactory found" {#the-container-fails-with-multiple-services-of-type-nette\application\ui\templatefactory-found}

Služba `latte.templateFactory` z Nette je stále autowirovaná. Pridajte k nej `autowired: no`, ako v [Konfigurácii](/sk/guide/configuration#the-two-service-entries). Autowirovaná môže byť len jedna služba daného typu.

## Premenná v jednej šablóne nie je definovaná, v ostatných funguje {#a-variable-is-undefined-in-one-template-but-works-in-others}

Táto šablóna nebola vytvorená cez dekorovanú továreň. Typické príčiny: služba, ktorá dostáva `@latte.templateFactory` podľa názvu namiesto autowirovanej `Nette\Application\UI\TemplateFactory`, šablóna vykreslená priamo cez `Latte\Engine` alebo šablóna vytvorená skôr, než bola premenná zaregistrovaná. Pozrite [Šablóny, ktoré premenné nedostanú](/sk/guide/templates#templates-that-do-not-get-the-variables).

## Môže presenter prepísať zaregistrovanú premennú? {#can-a-presenter-override-a-registered-variable}

Áno. Presenter priraďuje svoje premenné až po vytvorení šablóny, takže `$this->template->appName = 'Admin'` v šablóne tohto presentera vyhrá. Celé poradie ukazuje [príklad s prioritou hodnôt](/sk/examples/precedence).

## Môžem zaregistrovať `$user` alebo `$basePath`? {#can-i-register-user-or-basepath}

Môžete a zaregistrovaná hodnota potom v každej šablóne nahradí hodnotu z Nette, pretože zaregistrované premenné sa priraďujú po predvolených hodnotách Nette. Väčšinou je to chyba, zvoľte iný názov. S predvolenou triedou šablóny z Nette musí hodnota zodpovedať aj deklarovanému typu (`string` pre `$basePath`), inak `createTemplate()` vyhodí `TypeError`.

## Ako pridám filter alebo Latte funkciu do každej šablóny? {#how-do-i-add-a-filter-or-a-latte-function-to-every-template}

Pridajte [handler `onCreate`](/sk/guide/on-create), ktorý zavolá `$template->addFilter()` alebo `$template->addFunction()`. [Príklad onCreate](/sk/examples/on-create) pridáva filter `money`. Pre filtre, ktoré od šablóny nezávisia, funguje aj Latte rozšírenie zaregistrované v sekcii `latte: extensions:` v `config.neon`.

## Ako použijem translator v každej šablóne? {#how-do-i-use-a-translator-in-every-template}

Pridajte handler `onCreate`, ktorý zavolá `$template->setTranslator($translator)` (metóda triedy `Nette\Bridges\ApplicationLatte\Template`), alebo zaregistrujte `Latte\Essential\TranslatorExtension` z Latte v sekcii `latte: extensions:` v `config.neon`, na čo tento balík vôbec nepotrebujete.

## Vytvára sa zaregistrovaná služba pre každú šablónu? {#is-a-registered-service-created-for-every-template}

Nie. Služba sa vytvorí raz, keď sa vytvorí služba továrne, a každej šablóne sa priradí tá istá inštancia. Priradenie premennej je jeden zápis vlastnosti na premennú a šablónu, čo je oproti vykresľovaniu zanedbateľné.

## Spomaľuje registrácia služby každú požiadavku? {#does-registering-a-service-slow-down-every-request}

Len o cenu vytvorenia služby. Služba zaregistrovaná cez `@...` sa vytvorí s továrňou šablón pri každej požiadavke, ktorá vykresľuje šablónu, aj keď ju žiadna šablóna nevypíše. Pri ľahkej službe je to zadarmo. Službu s drahým konštruktorom označte `lazy: true` (PHP 8.4, nette/di 3.2.4+): šablóna dostane lazy ghost a konštruktor sa spustí až vtedy, keď ju šablóna prvýkrát použije. Pozrite [Drahé služby](/sk/guide/configuration#expensive-services-lazy-true) a [príklad s lenivou službou](/sk/examples/lazy-service).

## Počíta sa hodnota pre každú šablónu znova? {#is-the-value-computed-again-for-every-template}

Zaregistrované hodnoty sú pevné: literál, parameter aj výsledok `@service::method()` sa vyhodnotia raz. Pre hodnotu, ktorú treba počítať pre každú šablónu, použite `onCreate`; pre hodnotu, ktorá sa mení počas požiadavky, zaregistrujte službu a jej metódu volajte v šablóne (`{$cart->count()}`).

## Funguje to s typovanými triedami šablón a `{templateType}`? {#does-it-work-with-typed-template-classes-and-templatetype}

Áno. Trieda dostane zaregistrované premenné, ktoré deklaruje ako vlastnosti; tie, ktoré nedeklaruje, sa pre ňu preskočia, bez deprecation (ak nemá `#[\AllowDynamicProperties]`, vtedy dostane všetky). Pozrite [Vlastné triedy šablón](/sk/guide/template-classes).

## Funguje to bez Nette DI alebo mimo presenterov? {#does-it-work-without-nette-di-or-outside-presenters}

Áno. Konštruktor prijme akúkoľvek `Nette\Application\UI\TemplateFactory`; vytvorte továreň na Latte šablóny z Nette ručne a zavolajte `createTemplate()`. Pozrite [príklad bez DI](/sk/examples/without-di).

## Môžem zaregistrovanú premennú odstrániť? {#can-i-remove-a-registered-variable}

Nie, metóda na to neexistuje. Zaregistrujte názov znova s inou hodnotou alebo ho nastavte na šablóne v presenteri či v handleri `onCreate`.

## Môžem triedu zdediť? {#can-i-extend-the-class}

Nie, od verzie 4.0 je `final`. Nastavte ju cez `registerVariable()` a `onCreate` alebo napíšte vlastnú `TemplateFactory`, ktorá ju obalí. Pozrite [Prechod na 4.0](/sk/upgrade).

## Ktoré verzie sú podporované? {#which-versions-are-supported}

4.x vyžaduje PHP 8.4 alebo novšie a nette/application 3.2 alebo novšie a je testovaná na PHP 8.4, 8.5 a 8.6. 3.x podporuje PHP 8.1+; ktoré verzie dostávajú opravy, nájdete v [bezpečnostnej politike](https://github.com/tomaj/nette-auto-variable-template/blob/master/SECURITY.md).
