---
title: Prechod na 4.0
description: Ako prejsť z nette-auto-variable-template 3.x na 4.0 - PHP 8.4, final trieda a koniec Nette\SmartObject.
---

# Prechod z 3.x na 4.0 {#upgrading-from-3-x-to-4-0}

Verzia 4.0 zachováva konfiguráciu z 3.x: definícia služby v `config.neon`, `registerVariable()`, `getVariables()` aj `onCreate` fungujú ako predtým. Nekompatibilné zmeny sa týkajú platformy a samotnej triedy.

## PHP 8.4 alebo novšie {#php-8-4-or-newer}

4.0 vyžaduje PHP 8.4 (`"php": "^8.4"`; 3.x povoľovala PHP 8.1+). Kým nepoužívate podporovanú verziu PHP, zostaňte na 3.x.

Predtým (`composer.json`):

```json
"require": {
    "tomaj/nette-auto-variable-template": "^3.0"
}
```

Potom:

```json
"require": {
    "php": "^8.4",
    "tomaj/nette-auto-variable-template": "^4.0"
}
```

## Továreň je final {#the-factory-is-final}

`Tomaj\Latte\AutoVariableTemplateFactory` je `final`. Namiesto dedenia ju nastavte: premenné registrujte cez `registerVariable()` a kód pre každú šablónu spúšťajte cez `onCreate`.

Predtým:

<!-- snippet: skip -->
```php
use Nette\Application\UI\Control;
use Nette\Application\UI\Template;
use Tomaj\Latte\AutoVariableTemplateFactory;

class MyTemplateFactory extends AutoVariableTemplateFactory
{
    public function createTemplate(?Control $control = null, ?string $class = null): Template
    {
        $template = parent::createTemplate($control, $class);
        $template->addFilter('money', fn (float $amount): string => number_format($amount, 2) . ' €');

        return $template;
    }
}
```

Potom:

```php
use Nette\Application\UI\Template;
use Nette\Bridges\ApplicationLatte\Template as LatteTemplate;
use Tomaj\Latte\AutoVariableTemplateFactory;

$factory = new AutoVariableTemplateFactory($latteTemplateFactory);
$factory->onCreate[] = function (Template $template): void {
    if ($template instanceof LatteTemplate) {
        $template->addFilter('money', fn (float $amount): string => number_format($amount, 2) . ' €');
    }
};
```

V `config.neon` sa handler pridá riadkom setup:

```neon
services:
	templateFactory:
		factory: Tomaj\Latte\AutoVariableTemplateFactory(@latte.templateFactory)
		setup:
			- '$onCreate[]' = [@App\Templates\TemplateFilters, 'register']
```

Ak potrebujete zmeniť, čo vracia `createTemplate()`, továreň namiesto toho dekorujte: implementujte `Nette\Application\UI\TemplateFactory`, zo svojej triedy volajte `AutoVariableTemplateFactory::createTemplate()` a svoju triedu zaregistrujte ako autowirovanú továreň na šablóny.

## Koniec `Nette\SmartObject` {#no-more-nette\smartobject}

Trieda už nepoužíva `Nette\SmartObject`:

- `onCreate` je obyčajná typovaná vlastnosť `public array $onCreate`. Pridávanie handlerov sa nemení: `$factory->onCreate[] = $callback` v PHP alebo `'$onCreate[]' = ...` v `config.neon`. Priradenie niečoho, čo nie je pole, teraz vyhodí `TypeError`.
- Magická metóda `$factory->onCreate($template)`, ktorá spúšťala handlery, zmizla; `createTemplate()` ich volá sama. Ak váš kód spúšťal udalosť ručne, volajte handlery priamo.

Predtým:

<!-- snippet: skip -->
```php
$factory->onCreate($template);
```

Potom:

```php
foreach ($factory->onCreate as $handler) {
    $handler($template);
}
```

- Čítanie alebo zápis vlastnosti, ktorú trieda nedeklaruje, už nevyhadzuje `Nette\MemberAccessException`; PHP to hlási ako pri ktorejkoľvek inej triede.

`nette/utils` už nie je priama závislosť. Ak vaša aplikácia sama používa `Nette\Utils`, pridajte `nette/utils` do vlastného `composer.json` (nette/application ho stále nainštaluje).

## Typované triedy šablón dostanú len premenné, ktoré deklarujú {#typed-template-classes-get-only-the-variables-they-declare}

Nejde o nekompatibilnú zmenu pre kód bez deprecation, ale správanie je viditeľné. Verzia 3.x priraďovala každú zaregistrovanú premennú do každej šablóny; pri [typovo bezpečnej triede šablóny](/sk/guide/template-classes), ktorá premennú nedeklaruje, PHP hlásilo "Creation of dynamic property ... is deprecated" a hodnota sa aj tak priradila. Verzia 4.0 takú premennú pre danú triedu preskočí, bez deprecation. Triedy, ktoré vlastnosť deklarujú, majú `#[\AllowDynamicProperties]` (na triede alebo rodičovi, ako `DefaultTemplate` z Nette) alebo `__set()`, ju dostanú ako predtým.

Ak šablóna takej triedy číta zaregistrovanú premennú, ktorú nedeklaruje, deklarujte ju:

```php
use Nette\Bridges\ApplicationLatte\Template;

final class HomeTemplate extends Template
{
    public string $appName;
}
```

Úplný zoznam zmien je v [zozname zmien](/sk/changelog).
