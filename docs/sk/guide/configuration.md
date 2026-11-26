---
title: Konfigurácia
description: Všetky spôsoby, ako v config.neon zaregistrovať továreň a jej premenné, riadky setup, ktoré prijíma, a tá istá konfigurácia v čistom PHP.
---

# Konfigurácia {#configuration}

`AutoVariableTemplateFactory` nemá vlastnú konfiguračnú sekciu. Je to bežná služba: zaregistrujete ju v sekcii `services`, ako argument konštruktora jej odovzdáte továreň na šablóny z Nette a premenné pridáte riadkami `setup`.

## Dve položky služieb {#the-two-service-entries}

```neon
services:
	latte.templateFactory:
		autowired: no

	templateFactory:
		factory: Tomaj\Latte\AutoVariableTemplateFactory(@latte.templateFactory)
```

| Položka | Čo robí | Povinná |
|---|---|---|
| `latte.templateFactory: autowired: no` | Ponechá `Nette\Bridges\ApplicationLatte\TemplateFactory` z Nette (registruje ju rozšírenie pre Latte) ako službu, ale vypne jej autowiring. | Áno. Bez nej implementujú `Nette\Application\UI\TemplateFactory` dve služby a kontajner skončí chybou „Multiple services of type Nette\Application\UI\TemplateFactory found“. |
| `templateFactory: factory: ...(@latte.templateFactory)` | Zaregistruje dekorátor. Jeho jediným argumentom konštruktora je továreň, ktorá šablóny skutočne vytvára. Názov služby je na vás; vo všetkých príkladoch sa používa `templateFactory`. | Áno |

Presentery dostávajú továreň na šablóny cez `Presenter::injectPrimary()` a komponenty cez svoj presenter, takže po týchto dvoch položkách pochádza každé `$this->template` v aplikácii z `AutoVariableTemplateFactory`. Dostane ju aj kód, ktorý si `Nette\Application\UI\TemplateFactory` vyžiada podľa typu (napríklad odosielač e-mailov).

## Registrácia premenných {#registering-variables}

Každý riadok `registerVariable(name, value)` v sekcii `setup` pridá jednu premennú. Názov je `string` a stane sa názvom premennej v šablóne (`{$name}`); hodnota je čokoľvek, čo Nette DI vie odovzdať ako argument (`mixed`):

```neon
parameters:
	supportEmail: help@acme.test
	shop:
		name: Acme
		currencies: [EUR, CZK]

services:
	latte.templateFactory:
		autowired: no

	templateFactory:
		factory: Tomaj\Latte\AutoVariableTemplateFactory(@latte.templateFactory)
		setup:
			- registerVariable('appName', 'Acme Shop')                  # a literal
			- registerVariable('supportEmail', %supportEmail%)          # a parameter
			- registerVariable('shop', %shop%)                          # a whole parameter section, as an array
			- registerVariable('debug', %debugMode%)                    # a built-in parameter
			- registerVariable('settings', @App\Model\Settings)         # a service, by type
			- registerVariable('cart', @cart)                           # a service, by name
			- registerVariable('version', @App\Model\Settings::getVersion())  # a method result
			- registerVariable('menu', [home: 'Home', about: 'About us'])     # an array

	cart: App\Model\Cart
	- App\Model\Settings
```

Každá šablóna ich potom môže vypísať:

```latte
<title>{$appName}</title>
<a href="mailto:{$supportEmail}">{$supportEmail}</a> · {$shop['name']} accepts {implode(', ', $shop['currencies'])}
{if $debug}<p>Debug mode</p>{/if}
<p>Version {$version}, {$settings->version}, {$cart->count()} items</p>
<nav>{foreach $menu as $action => $label}<a n:href="$action">{$label}</a>{/foreach}</nav>
```

| Hodnota | Kedy sa vyhodnotí | Poznámka |
|---|---|---|
| Literál, pole, `%parameter%` | Pri kompilácii kontajnera | Pevná počas celej požiadavky. |
| `@service` | Pri vytvorení služby továrne (zvyčajne keď sa vytvára prvý presenter a dostáva továreň na šablóny) | V každej šablóne je tá istá inštancia služby; služba so stavom požiadavky (košík, aktuálny používateľ) ukazuje svoj stav v čase vykresľovania, pretože šablóny volajú jej metódy až pri vykresľovaní. |
| `@service::method()` | Raz, pri vytvorení služby továrne | Výsledok je pevný počas požiadavky. Pre hodnotu, ktorú treba počítať pre každú šablónu zvlášť, použite [`onCreate`](/sk/guide/on-create). |

Pravidlá, ktoré vyplývajú z kódu (`registerVariable()` ukladá hodnotu do poľa pod jej názvom):

- **Rovnaký názov znova hodnotu nahradí.** Vyhrá posledné `registerVariable()` pre daný názov, a to aj naprieč viacerými konfiguračnými súbormi (`common.neon`, `local.neon`), pretože Nette DI pripája ich riadky `setup` za sebou. [Príklad s prioritou](/sk/examples/precedence) registruje `theme` dvakrát.
- **Poradie sa zachová.** `getVariables()` vracia premenné v poradí, v akom boli prvýkrát zaregistrované, a v tomto poradí sa aj priraďujú do šablóny.
- **Dostanú ich len neskôr vytvorené šablóny.** Registrácia premennej nič nezmení na šablónach, ktoré už existujú.
- **Názvy sa nekontrolujú.** Ako názov funguje akýkoľvek reťazec, ale používajte platný názov PHP premennej, inak ju Latte nevypíše ako `{$name}`.

## Drahé služby: `lazy: true` {#expensive-services-lazy-true}

Služba zaregistrovaná cez `@...` sa vytvorí spolu s továrňou šablón, teda pri každej požiadavke, ktorá vykresľuje šablónu, bez ohľadu na to, či niektorá šablóna premennú vypíše. Pri službe s drahým konštruktorom (dopyt do databázy, vzdialená konfigurácia, klient CMS) sa táto cena platí na každej stránke, aj pri AJAX snippetoch a chybových stránkach.

S PHP 8.4 a nette/di 3.2.4 alebo novším označte službu `lazy: true`. Nette DI potom vytvorí [lazy ghost](https://www.php.net/manual/en/language.oop5.lazy-objects.php): objekt správnej triedy, ktorého konštruktor sa spustí až vtedy, keď niečo prvýkrát prečíta alebo zapíše niektorú jeho vlastnosť, napríklad v metóde, ktorá pracuje so stavom objektu. Volanie metódy, ktorá žiadnu vlastnosť nepoužije, ho neinicializuje. Zaregistrovaná premenná je tento neinicializovaný objekt, takže šablóna, ktorá ho nikdy nepoužije, zaňho nikdy nezaplatí. V tomto balíku ani v šablónach sa nič nemení:

```neon
services:
	templateFactory:
		factory: Tomaj\Latte\AutoVariableTemplateFactory(@latte.templateFactory)
		setup:
			- registerVariable('rates', @App\Model\ExchangeRates)

	exchangeRates:
		create: App\Model\ExchangeRates(%tempDir%/rates.cache)
		lazy: true
```

`di: lazy: true` v konfigurácii urobí lenivou každú službu, ktorá lenivá byť môže. Keď Nette DI službu lenivou urobiť nevie, vytvorí ju hneď, bez chyby:

- keď službu vytvára továrenská metóda alebo továrenská služba (`create: @factory::create()`), pretože ghost potrebuje triedu, ktorú vytvorí;
- keď je trieda vstavanou triedou PHP alebo z nej dedí (napríklad `ArrayObject`, `PDO`);
- keď služba nemá argumenty konštruktora ani riadky `setup`: nie je čo odložiť.

Odovzdanie objektu do `registerVariable()` a jeho priradenie do šablóny ho neinicializuje. Inicializuje ho až prvý prístup k jeho stavu, typicky prvé volanie metódy, ktorá číta vlastnosť, raz za požiadavku, ako v [príklade s lenivou službou](/sk/examples/lazy-service). Kód, ktorý objekt len kontroluje, napríklad `instanceof` alebo `get_class()`, ho tiež neinicializuje.

## Spustenie kódu pre každú šablónu {#running-code-for-every-template}

`$onCreate` je verejné pole callbackov; jeden pridáte riadkom v sekcii `setup`. Callback môže byť metóda služby, statická metóda alebo akýkoľvek PHP callable, ktorý Nette DI dokáže zapísať:

```neon
services:
	templateFactory:
		factory: Tomaj\Latte\AutoVariableTemplateFactory(@latte.templateFactory)
		setup:
			- registerVariable('currency', 'EUR')
			- '$onCreate[]' = [@App\Templates\TemplateSetup, 'setUp']

	- App\Templates\TemplateSetup
```

Všimnite si úvodzovky okolo `$onCreate[]`: NEON ich potrebuje, pretože riadok začína znakom `$`. Handlery sa spustia po priradení zaregistrovaných premenných; pozrite [Spúšťanie kódu: onCreate](/sk/guide/on-create).

## Názvy, ktoré Nette už používa {#names-nette-already-uses}

Továreň na šablóny z Nette nastavuje v každej šablóne tieto premenné, ak ich trieda šablóny deklaruje (predvolená šablóna z Nette áno): `$user`, `$baseUrl`, `$basePath`, `$flashes`, `$control` a `$presenter`. Zaregistrované premenné sa priraďujú až potom, takže registrácia niektorého z týchto názvov **nahradí hodnotu z Nette vo všetkých šablónach**. [Príklad s prioritou](/sk/examples/precedence) zámerne nahrádza `$basePath`. Ak to nie je váš zámer, zvoľte iné názvy.

Predvolená trieda šablóny ich deklaruje ako typované vlastnosti (`string $basePath`, `array $flashes`, ...). Zaregistrovaná hodnota iného typu alebo `null` spôsobí, že `createTemplate()` vyhodí `TypeError`, napríklad „Cannot assign int to property ...::$basePath of type string“.

## Viac tovární {#several-factories}

Nič vám nebráni zaregistrovať viac ako jednu `AutoVariableTemplateFactory`, napríklad jednu okolo druhej pre samostatnú továreň na e-maily s vlastnými premennými. Autowirovaná môže byť len jedna služba daného typu; ostatným nastavte `autowired: no` a odovzdávajte ich podľa názvu (`@mailTemplateFactory`).

## Čisté PHP {#plain-php}

Bez Nette DI vytvorte továreň s akoukoľvek implementáciou `Nette\Application\UI\TemplateFactory` a volajte tie isté metódy. Tu je `$latteTemplateFactory` továreň na Latte šablóny z Nette:

```php
use Nette\Application\UI\Template;
use Tomaj\Latte\AutoVariableTemplateFactory;

$factory = new AutoVariableTemplateFactory($latteTemplateFactory);
$factory->registerVariable('appName', 'Acme Shop')
    ->registerVariable('supportEmail', 'help@acme.test');
$factory->onCreate[] = function (Template $template): void {
    $template->greeting = 'Hello from ' . $template->appName;
};

echo $factory->createTemplate()->greeting;
```

<!-- snippet: output -->
```html
Hello from Acme Shop
```

[Príklad bez DI](/sk/examples/without-di) zostaví ručne aj továreň na Latte šablóny z Nette.
