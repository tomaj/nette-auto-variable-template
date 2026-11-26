---
title: Referencia API
description: Verejné API triedy Tomaj\Latte\AutoVariableTemplateFactory.
---

# Referencia API {#api-reference}

Balík obsahuje jedinú triedu. Okrem `nette/application` nemá žiadne vlastné závislosti a tvorí celé verejné API.

| Člen | Druh | Zhrnutie |
|---|---|---|
| [`__construct(TemplateFactory $innerTemplateFactory)`](#construct) | konštruktor | obalí továreň, ktorá vytvára šablóny |
| [`createTemplate(?Control $control = null, ?string $class = null): Template`](#createtemplate) | metóda | vytvorí šablónu, priradí premenné, spustí `onCreate` |
| [`registerVariable(string $key, mixed $variable): self`](#registervariable) | metóda | zaregistruje premennú pre každú odteraz vytvorenú šablónu |
| [`getVariables(): array`](#getvariables) | metóda | zaregistrované premenné |
| [`$onCreate`](#oncreate) | verejná vlastnosť | callbacky spúšťané pre každú novú šablónu |

## `Tomaj\Latte\AutoVariableTemplateFactory`

<!-- snippet: skip -->
```php
final class AutoVariableTemplateFactory implements Nette\Application\UI\TemplateFactory
```

Dekorátor okolo inej továrne na šablóny: nechá vnútornú továreň vytvoriť šablónu, priradí do nej zaregistrované premenné a zavolá handlery `onCreate`. Trieda je `final`; namiesto dedenia ju nakonfigurujte.

### `__construct()`

<!-- snippet: skip -->
```php
public function __construct(Nette\Application\UI\TemplateFactory $innerTemplateFactory)
```

`$innerTemplateFactory` vytvára šablóny, zvyčajne je to služba `latte.templateFactory` z Nette (`Nette\Bridges\ApplicationLatte\TemplateFactory`). Uloží sa do súkromnej `readonly` vlastnosti; getter neexistuje. Funguje akákoľvek implementácia rozhrania, aj ďalší dekorátor.

### `createTemplate()`

<!-- snippet: skip -->
```php
public function createTemplate(?Nette\Application\UI\Control $control = null, ?string $class = null): Nette\Application\UI\Template
```

Vytvorí šablónu:

1. zavolá `$innerTemplateFactory->createTemplate($control, $class)` (továreň na Latte šablóny z Nette prijíma triedu šablóny ako druhý argument);
2. priradí do šablóny každú zaregistrovanú premennú a nahradí hodnotu, ktorú pod rovnakým názvom nastavila vnútorná továreň;
3. zavolá každý handler `onCreate` so šablónou;
4. vráti šablónu.

| Parameter | Typ | Predvolená hodnota | Význam |
|---|---|---|---|
| `$control` | `?Nette\Application\UI\Control` | `null` | Presenter alebo komponent, ku ktorému šablóna patrí. Odovzdá sa bezo zmeny; továreň z Nette ho používa pre `$control`, `$presenter` a `$flashes`. |
| `$class` | `?string` | `null` | Trieda šablóny, ktorá sa má vytvoriť. Odovzdá sa bezo zmeny ako druhý argument, ktorý továreň na Latte šablóny z Nette prijíma, hoci rozhranie deklaruje len `$control`; `null` znamená predvolenú triedu továrne. |

**Vracia** šablónu vytvorenú vnútornou továrňou (ten istý objekt, nie kópiu).

**Vyhadzuje** sama nič. Prepúšťa ďalej:

- to, čo vyhodí vnútorná továreň, napríklad `Nette\InvalidArgumentException` z továrne Nette, keď `$class` neexistuje alebo nededí od `Nette\Bridges\ApplicationLatte\Template`;
- `TypeError`, keď zaregistrovaná hodnota nezodpovedá typovanej vlastnosti triedy šablóny (napríklad `null` pre `string $basePath`, pozrite [Vlastné triedy šablón](/sk/guide/template-classes#types-must-match));
- akúkoľvek výnimku vyhodenú handlerom `onCreate`; handlery za ním sa nespustia.

Zaregistrovaná premenná sa priradí len vtedy, keď trieda šablóny vlastnosť deklaruje, povoľuje dynamické vlastnosti (`#[\AllowDynamicProperties]` na triede alebo rodičovi) alebo má `__set()`; inak sa pre túto šablónu preskočí, bez deprecation. Pozrite [Vlastné triedy šablón](/sk/guide/template-classes).

Presentery a komponenty ju volajú cez `Control::createTemplate()`; sami ju voláte zriedka.

### `registerVariable()`

<!-- snippet: skip -->
```php
public function registerVariable(string $key, mixed $variable): self
```

| Parameter | Typ | Význam |
|---|---|---|
| `$key` | `string` | Názov premennej v šablóne (`{$key}`). Nekontroluje sa; používajte platný názov PHP premennej. |
| `$variable` | `mixed` | Hodnota. Objekty sa do každej šablóny priraďujú ako odkaz na tú istú inštanciu. |

Zaregistruje premennú pre každú odteraz vytvorenú šablónu a vráti továreň (`$this`), takže volania môžete reťaziť. Opätovná registrácia rovnakého `$key` nahradí hodnotu, ale ponechá jej pôvodnú pozíciu v poradí. Šablóny, ktoré už existujú, sa nezmenia. Metóda na odstránenie premennej neexistuje.

```php
$factory->registerVariable('appName', 'My Application')
    ->registerVariable('settings', new ArrayObject(['theme' => 'dark']));
```

### `getVariables()`

<!-- snippet: skip -->
```php
public function getVariables(): array
```

Vráti zaregistrované premenné ako `array<string, mixed>` (názov => hodnota) v poradí, v akom boli prvýkrát zaregistrované. Vrátené pole je kópia; jeho zmena továreň nezmení.

```php
$factory->registerVariable('appName', 'My Application');
var_dump($factory->getVariables() === ['appName' => 'My Application']);
```

<!-- snippet: output -->
```html
bool(true)
```

### `$onCreate`

<!-- snippet: skip -->
```php
/** @var array<callable(Nette\Application\UI\Template): void> */
public array $onCreate = [];
```

Callbacky, ktoré `createTemplate()` volá pre každú novú šablónu po priradení premenných, v poradí poľa. Jeden pridáte cez `$factory->onCreate[] = $callback` alebo v `config.neon` riadkom `- '$onCreate[]' = [@service, 'method']` v sekcii `setup`.

```php
use Nette\Application\UI\Template;

$factory->onCreate[] = function (Template $template): void {
    $template->now = new DateTimeImmutable();
};
```

`$onCreate` je obyčajná typovaná vlastnosť (od 4.0 bez mágie `Nette\SmartObject`): priradenie čohokoľvek iného ako poľa vyhodí `TypeError` a metóda `$factory->onCreate($template)` neexistuje. Pozrite [Spúšťanie kódu: onCreate](/sk/guide/on-create).

## Nie je súčasťou API {#not-part-of-the-api}

- `private` metóda `assignVariables()` a `private` vlastnosti sa môžu zmeniť v ľubovoľnom vydaní.
- Trieda je `final`. Namiesto dedenia ju nakonfigurujte alebo ju obaľte vlastnou implementáciou `Nette\Application\UI\TemplateFactory` ([Prechod na 4.0](/sk/upgrade#the-factory-is-final)).
