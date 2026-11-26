---
layout: home
title: Nette Auto Variable Template
description: Zaregistrujte premenné raz a používajte ich v každej Latte šablóne vašej Nette aplikácie, v presenteroch, layoutoch, komponentoch aj e-mailoch.

hero:
  name: Nette Auto Variable Template
  text: Zaregistrujte raz, použite v každej šablóne
  tagline: Továreň na šablóny pre Nette, ktorá vaše premenné odovzdá každej Latte šablóne, v presenteroch, layoutoch, komponentoch aj v šablónach, ktoré si vytvoríte sami. Nastavuje sa v config.neon, bez základného presentera.
  image:
    light: /illustrations/hero-sk-light.svg
    dark: /illustrations/hero-sk-dark.svg
    alt: Jeden riadok registerVariable v config.neon prechádza cez AutoVariableTemplateFactory do layoutu, komponentu a e-mailovej šablóny, ktoré všetky vypíšu názov aplikácie
  actions:
    - theme: brand
      text: Začíname
      link: /sk/guide/getting-started
    - theme: alt
      text: Príklady
      link: /sk/examples/
    - theme: alt
      text: GitHub
      link: https://github.com/tomaj/nette-auto-variable-template

features:
  - icon:
      light: /icons/config.svg
      dark: /icons/config-dark.svg
      width: 48
      height: 48
    title: Nastavené v config.neon
    details: Obaľte továreň na šablóny z Nette dvoma záznamami služieb a cez riadky setup zaregistrujte hodnoty, parametre alebo služby.
    link: /sk/guide/configuration
    linkText: Konfigurácia
  - icon:
      light: /icons/templates.svg
      dark: /icons/templates-dark.svg
      width: 48
      height: 48
    title: Každá šablóna
    details: Šablóny presenterov a layouty, šablóny komponentov aj šablóny pre e-maily dostanú rovnaké premenné.
    link: /sk/guide/templates
    linkText: Presentery a komponenty
  - icon:
      light: /icons/oncreate.svg
      dark: /icons/oncreate-dark.svg
      width: 48
      height: 48
    title: Handlery onCreate
    details: Spustite vlastný kód pre každú novú šablónu po priradení premenných, napríklad na pridanie filtrov alebo hodnôt počítaných pre šablónu.
    link: /sk/guide/on-create
    linkText: onCreate
  - icon:
      light: /icons/layers.svg
      dark: /icons/layers-dark.svg
      width: 48
      height: 48
    title: Predvídateľné poradie
    details: Zaregistrované hodnoty nahradia predvolené hodnoty Nette, onCreate beží po nich a presenter alebo komponent ich stále môže prepísať.
    link: /sk/guide/how-it-works
    linkText: Ako to funguje
  - icon:
      light: /icons/typed.svg
      dark: /icons/typed-dark.svg
      width: 48
      height: 48
    title: Funguje s typovanými šablónami
    details: Deklarujte zaregistrované premenné vo vašich triedach šablón a PHPStan aj {templateType} z Latte budú spokojné.
    link: /sk/guide/template-classes
    linkText: Vlastné triedy šablón
  - icon:
      light: /icons/cube.svg
      dark: /icons/cube-dark.svg
      width: 48
      height: 48
    title: Jedna malá final trieda
    details: Dekorátor okolo Nette\Application\UI\TemplateFactory s tromi metódami a jednou udalosťou. Testovaný na PHP 8.4 až 8.6.
    link: /sk/api/reference
    linkText: Referencia API
---

<div class="home-more">

## Ako to vyzerá {#what-it-looks-like}

Zaregistrujte premenné raz v `config.neon`:

```neon
services:
	latte.templateFactory:
		autowired: no

	templateFactory:
		factory: Tomaj\Latte\AutoVariableTemplateFactory(@latte.templateFactory)
		setup:
			- registerVariable('appName', 'Acme Shop')
			- registerVariable('settings', @App\Model\Settings)
```

Layout aj každá šablóna stránky ich používajú a presenter nastavuje len `$title`:

<<< @/../examples/hello-world/app/Presenters/templates/@layout.latte{latte}

`GET /` vykreslí toto (výňatok z výstupu [príkladu hello world](/sk/examples/hello-world), ktorý CI kontroluje pri každej zmene):

```html
<title>Welcome | Acme Shop</title>
<header>Acme Shop</header>
<footer>Acme Shop 4.0.0 · <a href="mailto:help@acme.test">help@acme.test</a></footer>
```

## Kam ďalej {#where-to-go-next}

<LinkCards :items="[
  { title: 'Začíname', text: 'Nainštalujte balík, zaregistrujte ho v config.neon a vykreslite prvú stránku.', link: '/sk/guide/getting-started' },
  { title: 'Ako to funguje', text: 'Čo sa deje pri vytváraní šablóny a ktorá hodnota vyhrá.', link: '/sk/guide/how-it-works' },
  { title: 'Príklady', text: 'Osem spustiteľných Nette aplikácií a skriptov, testovaných v CI.', link: '/sk/examples/' },
  { title: 'Referencia API', text: 'Konštruktor, createTemplate(), registerVariable(), getVariables() a onCreate.', link: '/sk/api/reference' },
  { title: 'Časté otázky', text: 'Chyby autowiringu, vlastné premenné Nette, translatory, cache a ďalšie.', link: '/sk/faq' },
  { title: 'Prechod na 4.0', text: 'PHP 8.4, final trieda a koniec SmartObject.', link: '/sk/upgrade' },
]" />

</div>
