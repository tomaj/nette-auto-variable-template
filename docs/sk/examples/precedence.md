---
title: "Ktorá hodnota vyhrá"
description: "Rovnaké premenné nastavené cez onCreate z Nette, registerVariable, onCreate továrne a presenter: príklad vypíše, s ktorou hodnotou každá šablóna skončí."
---

# Ktorá hodnota vyhrá {#which-value-wins}

<ExampleSource name="precedence" />

Tento príklad nastavuje premenné v každom kroku [vytvárania šablóny](/sk/guide/how-it-works) a vypíše ich, takže vidíte, ktorá hodnota vyhrá: tá z neskoršieho kroku.

## Konfigurácia {#configuration}

<<< @/../examples/precedence/config/common.neon{neon}

## Handlery {#the-handlers}

<<< @/../examples/precedence/app/Templates/Stages.php{php}

## Presenter a šablóna {#presenter-and-template}

<<< @/../examples/precedence/app/Presenters/HomePresenter.php{php}

<<< @/../examples/precedence/app/Presenters/templates/Home/default.latte{latte}

`plain.latte` je rovnaká šablóna pre akciu `plain`, ktorej `renderPlain()` nenastavuje nič.

## Výstup {#output}

<<< @/../examples/precedence/expected-output.txt{html}

- `z`: nastavuje ju len `onCreate` služby `latte.templateFactory` z Nette, takže si túto hodnotu ponechá.
- `a`: `onCreate` z Nette beží vo vnútornej továrni ešte pred priradením zaregistrovaných premenných, takže vyhrá `registered`.
- `b`: `onCreate` továrne beží po zaregistrovaných premenných, takže vyhrá `onCreate`.
- `c`: na `/` ju `renderDefault()` nastaví, keď už šablóna existuje, takže vyhrá `presenter`; na `/home/plain` ju nenastaví nič, takže zostane `onCreate`.
- `theme`: zaregistrovaná dvakrát; druhá registrácia nahradila prvú.
- `basePath`: Nette ju nastaví na základnú cestu požiadavky (tu prázdnu) a zaregistrovaná hodnota `/static` ju nahradí v každej šablóne. `baseUrl` zaregistrovaná nie je, takže zostane hodnota z Nette.
