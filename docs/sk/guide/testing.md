---
title: Testovanie
description: Otestujte pomocou PHPUnit, že vaša Nette aplikácia registruje premenné šablón a že ich presentery a komponenty vykresľujú.
---

# Testovanie integrácie {#testing-your-integration}

V aplikácii, ktorá túto továreň používa, sa oplatí otestovať tri veci:

1. kontajner autowiruje `AutoVariableTemplateFactory` ako továreň na šablóny (riadok `autowired: no` je na svojom mieste),
2. očakávané premenné sú zaregistrované so správnymi hodnotami,
3. presenter ich vykreslí.

[Príklad s testovaním](/sk/examples/testing) robí všetky tri pomocou PHPUnit so skutočným `config/common.neon` aplikácie:

<<< @/../examples/testing/tests/TemplateVariablesTest.php{php}

`php run.php` spustí PHPUnit a vypíše:

<<< @/../examples/testing/expected-output.txt{text}

## Bez kontajnera {#without-a-container}

Na unit test handlera `onCreate` alebo kódu, ktorý vykresľuje šablónu, zostavte továreň ručne okolo továrne na Latte šablóny z Nette. `$latteTemplateFactory` je tu `Nette\Bridges\ApplicationLatte\TemplateFactory` (ako ju vytvoriť, ukazuje [príklad bez DI](/sk/examples/without-di)):

```php
use Nette\Application\UI\Template;
use Tomaj\Latte\AutoVariableTemplateFactory;

$factory = new AutoVariableTemplateFactory($latteTemplateFactory);
$factory->registerVariable('appName', 'Test Shop');
$factory->onCreate[] = static function (Template $template): void {
    $template->title = strtoupper($template->appName);
};

$template = $factory->createTemplate();
var_dump($template->appName === 'Test Shop', $template->title === 'TEST SHOP');
```

<!-- snippet: output -->
```html
bool(true)
bool(true)
```

## Test, že premenná neunikne {#testing-that-a-variable-is-not-leaked}

Ak sa hodnota nikdy nesmie dostať do šablón (pozrite [Bezpečnosť](/sk/guide/security)), overte, že ju `getVariables()` neobsahuje:

```php
$factory->registerVariable('appName', 'Acme Shop');

var_dump(array_key_exists('apiKey', $factory->getVariables()));
```

<!-- snippet: output -->
```html
bool(false)
```
