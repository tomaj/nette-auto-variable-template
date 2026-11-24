---
title: Bezpečnosť
description: Čo neregistrovať ako premennú šablóny, escapovanie a ako nahlásiť zraniteľnosť v nette-auto-variable-template.
---

# Bezpečnosť {#security}

## Zaregistrované premenné sa dostanú do každej šablóny {#registered-variables-reach-every-template}

Každá šablóna vytvorená cez túto továreň dostane každú zaregistrovanú premennú: vaše presentery a komponenty, ale aj komponenty a šablóny z balíkov tretích strán, chybové stránky, e-mailové šablóny a čokoľvek ďalšie, čo používa autowirovanú továreň na šablóny. Zaregistrovanú premennú berte ako verejnú pre všetok kód šablón.

- **Neregistrujte tajné údaje**: heslá, API kľúče, tokeny ani konfiguračné polia a služby, ktoré ich obsahujú (`%database%`, celú sekciu `%parameters%`, mailer nastavený s prihlasovacími údajmi k SMTP). Stačí, aby šablóna omylom vypísala `{dump}` alebo `{$config|json}`, v debug režime alebo do logu, a uniknú.
- **Registrujte úzke služby.** Namiesto kontajnera, entity managera alebo služby s metódami, ktoré menia dáta, zaregistrujte malý nemenný objekt s hodnotami, ktoré šablóny potrebujú (`Settings` s `version` a `supportEmail` v [príkladoch](/sk/examples/hello-world)).
- **Údaje používateľa platia pre jednu požiadavku.** Služba ako aktuálny používateľ alebo košík je v každej šablóne požiadavky tá istá inštancia. V PHP-FPM, kde má každá požiadavka vlastný kontajner, je to v poriadku. V dlho bežiacom procese (RoadRunner, Swoole, worker, ktorý vykresľuje e-maily pre mnohých používateľov) zabezpečte, aby sa taká služba medzi požiadavkami alebo používateľmi resetovala, alebo hodnotu počítajte v [`onCreate`](/sk/guide/on-create).

## Escapovanie {#escaping}

Továreň hodnoty len priraďuje. Latte ich escapuje pri vypísaní v šablóne podľa kontextu (`{$appName}` v HTML texte, v atribúte, v JavaScripte), rovnako ako akúkoľvek inú premennú. Neregistrujte vopred vykreslené HTML ako reťazec, ktorý potom vypisujete cez `|noescape`; zaregistrujte dáta a HTML zostavte v šablóne.

## Nahlásenie zraniteľnosti {#reporting-a-vulnerability}

Zraniteľnosti hláste neverejne cez [súkromné hlásenie zraniteľností na GitHube](https://github.com/tomaj/nette-auto-variable-template/security/advisories/new), ako popisuje [bezpečnostná politika (SECURITY.md)](https://github.com/tomaj/nette-auto-variable-template/blob/master/SECURITY.md), kde nájdete aj podporované verzie. Neotvárajte verejné issue.
