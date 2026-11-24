---
title: Ako to funguje
description: Čo sa stane, keď sa Nette šablóna vytvára cez AutoVariableTemplateFactory, a ktorá hodnota vyhrá, keď rovnakú premennú nastaví Nette, zaregistrované premenné, onCreate aj presenter.
---

# Ako to funguje {#how-it-works}

`AutoVariableTemplateFactory` je [dekorátor](https://en.wikipedia.org/wiki/Decorator_pattern): implementuje rovnaké rozhranie ako továreň na šablóny z Nette (`Nette\Application\UI\TemplateFactory`), v konštruktore dostane továreň z Nette a do `createTemplate()` pridá jeden krok. Celá metóda má štyri riadky:

<!-- snippet: skip -->
```php
// src/AutoVariableTemplateFactory.php, simplified
public function createTemplate(?Control $control = null, ?string $class = null): Template
{
    $template = $this->innerTemplateFactory->createTemplate($control, $class);
    $this->assignVariables($template);   // $template->$name = $value for every registered variable
    foreach ($this->onCreate as $handler) {
        $handler($template);
    }
    return $template;
}
```

## Čo sa deje pri vytváraní šablóny {#what-happens-when-a-template-is-created}

![Vytvorenie šablóny: presenter alebo komponent zavolá createTemplate, továreň na Latte šablóny z Nette ju vytvorí s predvolenými premennými a vlastným onCreate, priradia sa zaregistrované premenné, spustia sa handlery onCreate a nakoniec si presenter alebo komponent nastaví vlastné premenné.](/diagrams/lifecycle-sk-light.svg){.only-light}
![Vytvorenie šablóny: presenter alebo komponent zavolá createTemplate, továreň na Latte šablóny z Nette ju vytvorí s predvolenými premennými a vlastným onCreate, priradia sa zaregistrované premenné, spustia sa handlery onCreate a nakoniec si presenter alebo komponent nastaví vlastné premenné.](/diagrams/lifecycle-sk-dark.svg){.only-dark}

1. **Presenter alebo komponent potrebuje šablónu.** `$this->template` sa vytvorí pri prvom použití cez `Control::createTemplate()`, ktorá zavolá autowirovanú továreň na šablóny s komponentom a triedou šablóny (`HomeTemplate`, ak taká trieda existuje, pozrite [Vlastné triedy šablón](/sk/guide/template-classes)).
2. **Šablónu vytvorí továreň z Nette.** `Nette\Bridges\ApplicationLatte\TemplateFactory` vytvorí Latte engine a objekt šablóny, nastaví `$user`, `$baseUrl`, `$basePath`, `$flashes`, `$control` a `$presenter`, ak ich trieda šablóny deklaruje, a zavolá vlastné handlery `onCreate` (tie, ktoré pridávate do `latte.templateFactory`).
3. **Priradia sa zaregistrované premenné** v poradí, v akom boli zaregistrované. Zaregistrovaný názov, ktorý Nette už nastavilo v kroku 2, nahradí hodnotu z Nette.
4. **Spustia sa handlery `onCreate` triedy `AutoVariableTemplateFactory`** v poradí, v akom boli pridané. Vidia zaregistrované premenné a môžu ich meniť alebo pridávať nové premenné, filtre a funkcie.
5. **Presenter alebo komponent si nastaví vlastné premenné** v `action*()`, `render*()` alebo vo svojej metóde `render()`. Šablóna už v tej chvíli existuje, takže tieto priradenia prepíšu čokoľvek z predchádzajúcich krokov.

Šablóny vytvorené mimo presentera, napríklad cez `$templateFactory->createTemplate()` pre e-mail, prejdú krokmi 2 až 4; neexistuje žiadny komponent, takže `$control` a `$presenter` nie sú nastavené.

## Ktorá hodnota vyhrá {#which-value-wins}

Vyhrá neskorší krok. [Príklad s prioritou](/sk/examples/precedence) nastaví premenné v každom kroku a vypíše ich:

<<< @/../examples/precedence/config/common.neon{neon}

<<< @/../examples/precedence/app/Templates/Stages.php{php}

<<< @/../examples/precedence/app/Presenters/HomePresenter.php{php}

Výstup:

<<< @/../examples/precedence/expected-output.txt{html}

| Premenná | Nastavuje | Hodnota v šablóne | Prečo |
|---|---|---|---|
| `z` | len `onCreate` z Nette | `Nette onCreate` | Nič neskôr ju nenastaví. |
| `a` | `onCreate` z Nette, potom registrácia | `registered` | Krok 3 beží po kroku 2. |
| `b` | registrácia, potom `onCreate` | `onCreate` | Krok 4 beží po kroku 3. |
| `c` | registrácia, `onCreate`, presenter (len `/`) | `presenter` na `/`, `onCreate` na `/home/plain` | Presenter ju nastaví po vytvorení šablóny, ale len v `renderDefault()`. |
| `theme` | dvakrát zaregistrovaná | `dark` | Druhé `registerVariable('theme', ...)` nahradilo prvé. |
| `basePath` | Nette, potom registrácia | `/static` | Zaregistrovaná premenná nahradí predvolenú hodnotu z Nette. |
| `baseUrl` | len Nette | `http://localhost:8000` | Nie je zaregistrovaná, takže zostane hodnota z Nette. |

## Čo nerobí {#what-it-does-not-do}

- Nemení šablóny, ktoré už existujú. Premenná zaregistrovaná počas behu aplikácie sa dostane len do šablón vytvorených potom.
- Nič nevykresľuje a o Latte nič nevie: pracuje s akoukoľvek `Nette\Application\UI\Template`, ktorú vráti vnútorná továreň. Filtre a funkcie sú vlastnosti Latte, preto handlery `onCreate` pred volaním `addFilter()` overujú, či ide o `Nette\Bridges\ApplicationLatte\Template`.
- Hodnoty nekopíruje ani neklonuje. Každá šablóna dostane rovnaký objekt služby alebo objektovej hodnoty; šablóna, ktorá taký objekt zmení, ho zmení aj pre ostatné.
- Ručné vykresľovanie vlastným Latte enginom (`$latte->render('file.latte', $params)`) továreň na šablóny nepoužíva, takže premenné v ňom nebudú. Použite namiesto toho `$templateFactory->createTemplate()`, ako v [príklade bez DI](/sk/examples/without-di).
