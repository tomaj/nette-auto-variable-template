---
title: 'Spúšťanie kódu: onCreate'
description: Spúšťajte vlastné callbacky pre každú šablónu vytvorenú cez AutoVariableTemplateFactory, po priradení zaregistrovaných premenných, pre filtre, funkcie a hodnoty počítané pre každú šablónu.
---

# Spúšťanie kódu: `onCreate` {#running-code-oncreate}

`$onCreate` je verejné pole callable hodnôt na továrni:

<!-- snippet: skip -->
```php
/** @var array<callable(Nette\Application\UI\Template): void> */
public array $onCreate = [];
```

`createTemplate()` zavolá každý z nich s novou šablónou, a to **po** priradení zaregistrovaných premenných, v poradí poľa. Návratová hodnota sa ignoruje. Použite ho na to, čo pevná hodnota nedokáže:

- pridať Latte filtre a funkcie do každej šablóny,
- vypočítať hodnotu pre konkrétnu šablónu (z komponentu, presentera alebo aktuálnej požiadavky),
- prečítať zaregistrované premenné a niečo z nich odvodiť.

## V PHP {#in-php}

```php
use Nette\Application\UI\Template;
use Nette\Bridges\ApplicationLatte\Template as LatteTemplate;

$factory->onCreate[] = function (Template $template): void {
    $template->generatedAt = new DateTimeImmutable('2026-11-24 10:00');

    if ($template instanceof LatteTemplate) {
        $template->addFilter('money', fn (float $amount): string => number_format($amount, 2) . ' €');
    }
};

$template = $factory->createTemplate();
echo $template->appName, ', ', $template->generatedAt->format('Y-m-d');
```

<!-- snippet: output -->
```html
My Application, 2026-11-24
```

Typ parametra je rozhranie `Nette\Application\UI\Template`. `addFilter()`, `addFunction()` a `getLatte()` existujú len na `Nette\Bridges\ApplicationLatte\Template`, ktorú továreň z Nette vracia vždy; overte ju ako vyššie, aby PHPStan vedel, že metóda existuje.

## V config.neon {#in-config-neon}

Ako handler pridajte metódu služby riadkom v sekcii `setup`. Z [príkladu onCreate](/sk/examples/on-create):

<<< @/../examples/on-create/config/common.neon{neon}

<<< @/../examples/on-create/app/Templates/TemplateSetup.php{php}

Handler prečíta `$currency`, ktorá bola zaregistrovaná pred ním, pridá filter `money`, ktorý ju používa, a nastaví `$cssClass` podľa komponentu, ku ktorému šablóna patrí. Stránka a komponent vypíšu:

<<< @/../examples/on-create/expected-output.txt{html}

## Poradie {#order}

- Všetky zaregistrované premenné sa priradia pred spustením prvého handlera, takže ich môže čítať každý handler.
- Handlery sa spúšťajú v poradí, v akom boli pridané; neskorší handler vidí, čo nastavili predchádzajúce.
- `onCreate` vlastnej služby `latte.templateFactory` z Nette beží skôr, vo vnútornej továrni, ešte pred zaregistrovanými premennými. Zaregistrovaná premenná preto nahradí hodnotu, ktorú tam nastavíte. Pozrite [Ako to funguje](/sk/guide/how-it-works#which-value-wins).
- Presenter alebo komponent priraďuje svoje premenné až potom, čo `createTemplate()` skončí, takže stále môže prepísať to, čo nastavil handler.

## Výnimky {#exceptions}

`createTemplate()` nič nezachytáva: výnimka vyhodená handlerom sa dostane k volajúcemu (pri presenteri požiadavka skončí touto výnimkou) a handlery za ním sa nespustia.

## onCreate alebo registerVariable? {#oncreate-or-registervariable}

| Potreba | Použite |
|---|---|
| Rovnaká hodnota alebo služba v každej šablóne | `registerVariable()` |
| Filter alebo funkcia v každej šablóne | `onCreate` (alebo vlastný mechanizmus rozšírení v Latte) |
| Hodnota, ktorá závisí od šablóny, komponentu alebo presentera | `onCreate` |
| Hodnota, ktorá sa počíta raz za požiadavku a je lacná | `registerVariable()` so službou a volaním metódy v šablóne (`{$settings->version}`) |
