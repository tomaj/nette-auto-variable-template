#!/usr/bin/env python3
"""Writes the hand-made SVGs of the site, each in a light and a dark variant:

- public/illustrations/hero-*.svg: one registerVariable() line in config.neon, the factory, and three
  templates (a presenter layout, a component, an e-mail) that all print {$appName}
- public/icons/<name>.svg and <name>-dark.svg: the feature icons of the home page
- public/diagrams/lifecycle-*.svg: what happens, in order, when a template is created

The hero and the diagram have text, so they are also written in Slovak (hero-sk-*.svg, lifecycle-sk-*.svg)
for the pages under docs/sk/.

Run: python3 scripts/illustrations.py, then python3 scripts/check-svg.py
"""
from pathlib import Path

OUT = Path(__file__).resolve().parents[1] / "public" / "illustrations"

THEMES = {
    "light": dict(card="#ffffff", stroke="#5eead4", title="#f0fdfa", text="#134e4a", muted="#64748b",
                  chip="#fef3c7", chipText="#92400e", factory="#0f766e", factoryText="#ffffff", arrow="#14b8a6",
                  key="#0f766e", string="#b45309", glow="#ccfbf1"),
    "dark": dict(card="#1f2428", stroke="#2dd4bf", title="#16302e", text="#e2e8f0", muted="#94a3b8",
                 chip="#451a03", chipText="#fcd34d", factory="#134e4a", factoryText="#ccfbf1", arrow="#2dd4bf",
                 key="#5eead4", string="#fbbf24", glow="#134e4a"),
}

MONO = "ui-monospace, SFMono-Regular, Menlo, Consolas, monospace"
SANS = "Inter, system-ui, -apple-system, 'Segoe UI', sans-serif"


def card(x, y, w, h, name, lines, c):
    out = [
        f'<g transform="translate({x} {y})">',
        f'<rect width="{w}" height="{h}" rx="12" fill="{c["card"]}" stroke="{c["stroke"]}" stroke-width="1.5"/>',
        f'<path d="M0 12a12 12 0 0 1 12-12h{w-24}a12 12 0 0 1 12 12v16H0z" fill="{c["title"]}"/>',
        f'<circle cx="14" cy="14" r="3.5" fill="{c["stroke"]}"/>',
        f'<text x="26" y="18.5" font-family="{MONO}" font-size="11.5" fill="{c["muted"]}">{name}</text>',
    ]
    for i, parts in enumerate(lines):
        ty = 50 + i * 22
        tx = 14
        for kind, text in parts:
            width = len(text) * 7.2
            if kind == "chip":
                out.append(f'<rect x="{tx-3}" y="{ty-14}" width="{width+6}" height="20" rx="5" fill="{c["chip"]}"/>')
                out.append(f'<text x="{tx}" y="{ty}" font-family="{MONO}" font-size="12" font-weight="700" fill="{c["chipText"]}">{text}</text>')
            else:
                color = {"t": c["text"], "m": c["muted"], "k": c["key"], "s": c["string"]}[kind]
                out.append(f'<text x="{tx}" y="{ty}" font-family="{MONO}" font-size="12" fill="{color}" xml:space="preserve">{text}</text>')
            tx += width
    out.append("</g>")
    return "\n".join(out)


HERO_TEXT = {
    "en": dict(
        aria="One registerVariable line in config.neon goes through AutoVariableTemplateFactory into every template: a presenter layout, a component and an e-mail all print the app name",
        email=["Hello {$name},", "thanks for", "shopping at"],
        labels=["presenter", "component", "any template"],
    ),
    "sk": dict(
        aria="Jeden riadok registerVariable v config.neon prejde cez AutoVariableTemplateFactory do každej šablóny: layout presentera, komponent aj e-mail vypíšu názov aplikácie",
        email=["Dobrý deň {$name},", "ďakujeme za", "nákup v"],
        labels=["presenter", "komponent", "akákoľvek šablóna"],
    ),
}


def svg(c, t=HERO_TEXT["en"]):
    parts = [
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 560 460" width="560" height="460" role="img" '
        f'aria-label="{t["aria"]}">',
        f'<circle cx="280" cy="214" r="200" fill="{c["glow"]}" opacity="0.55"/>',
        card(60, 14, 440, 112, "config.neon", [
            [("k", "templateFactory"), ("m", ":")],
            [("m", "  setup:")],
            [("m", "    - registerVariable("), ("s", "'appName'"), ("m", ", "), ("s", "'Acme'"), ("m", ")")],
        ], c),
        f'<path d="M280 128v28" stroke="{c["arrow"]}" stroke-width="2.5" stroke-linecap="round"/>',
        f'<path d="M273 150l7 9 7-9" fill="none" stroke="{c["arrow"]}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>',
        '<g transform="translate(130 164)">',
        f'<rect width="300" height="52" rx="26" fill="{c["factory"]}" stroke="{c["arrow"]}" stroke-width="1.5"/>',
        f'<g transform="translate(14 10) scale(0.5)" fill="none" stroke-linecap="round" stroke-linejoin="round">'
        f'<path d="M21 15c-5 0-6 2-6 7v4c0 4-2 6-5 6 3 0 5 2 5 6v4c0 5 1 7 6 7M43 15c5 0 6 2 6 7v4c0 4 2 6 5 6-3 0-5 2-5 6v4c0 5-1 7-6 7" stroke="{c["factoryText"]}" stroke-width="4.5"/>'
        f'<path d="M37.5 25.5c-1-2.6-3-3.5-5.5-3.5-3.2 0-5.5 1.700-5.5 4.300 0 3 2.600 3.800 5.5 4.400 3.100.6 5.800 1.600 5.800 4.700 0 2.800-2.400 4.600-5.800 4.600-2.700 0-4.900-1.100-5.900-3.700M32 17.5v29" stroke="#fbbf24" stroke-width="3.6"/></g>',
        f'<text x="54" y="31" font-family="{SANS}" font-size="15" font-weight="600" fill="{c["factoryText"]}">AutoVariableTemplateFactory</text>',
        "</g>",
    ]
    for x2 in (100, 280, 460):
        parts.append(f'<path d="M280 218C280 240 {x2} 234 {x2} 260" fill="none" stroke="{c["arrow"]}" stroke-width="2.5" stroke-linecap="round"/>')
        parts.append(f'<path d="M{x2-7} 255l7 9 7-9" fill="none" stroke="{c["arrow"]}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>')
    parts += [
        card(16, 270, 168, 150, "@layout.latte", [
            [("m", "&lt;title&gt;")],
            [("t", "  "), ("chip", "{$appName}")],
            [("m", "&lt;/title&gt;")],
            [("m", "{include content}")],
        ], c),
        card(196, 270, 168, 150, "menu.latte", [
            [("m", "&lt;nav&gt;")],
            [("t", "  "), ("chip", "{$appName}")],
            [("m", "  {foreach …}")],
            [("m", "&lt;/nav&gt;")],
        ], c),
        card(376, 270, 168, 150, "email.latte", [
            [("t", t["email"][0])],
            [("t", t["email"][1])],
            [("t", t["email"][2])],
            [("chip", "{$appName}"), ("t", ".")],
        ], c),
        f'<text x="100" y="444" text-anchor="middle" font-family="{SANS}" font-size="12" fill="{c["muted"]}">{t["labels"][0]}</text>',
        f'<text x="280" y="444" text-anchor="middle" font-family="{SANS}" font-size="12" fill="{c["muted"]}">{t["labels"][1]}</text>',
        f'<text x="460" y="444" text-anchor="middle" font-family="{SANS}" font-size="12" fill="{c["muted"]}">{t["labels"][2]}</text>',
        "</svg>",
    ]
    return "\n".join(parts) + "\n"


ICONS = {
    # config.neon: a file with key/value lines
    "config": lambda c: (
        f'<path d="M12 6h17l9 9v27a2 2 0 0 1-2 2H12a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z" fill="{c["fill"]}" stroke="{c["line"]}" stroke-width="2.5" stroke-linejoin="round"/>'
        f'<path d="M29 6v9h9" fill="none" stroke="{c["line"]}" stroke-width="2.5" stroke-linejoin="round"/>'
        f'<path d="M16 22h8M16 29h12M16 36h6" stroke="{c["line"]}" stroke-width="2.5" stroke-linecap="round"/>'
        f'<circle cx="31" cy="36" r="3.5" fill="{c["accent"]}"/>'
    ),
    # every template: three pages, each with a variable
    "templates": lambda c: (
        f'<rect x="18" y="4" width="24" height="30" rx="3" fill="{c["fill"]}" stroke="{c["line"]}" stroke-width="2.5"/>'
        f'<rect x="12" y="10" width="24" height="30" rx="3" fill="{c["fill"]}" stroke="{c["line"]}" stroke-width="2.5"/>'
        f'<rect x="6" y="16" width="24" height="28" rx="3" fill="{c["fill"]}" stroke="{c["line"]}" stroke-width="2.5"/>'
        f'<rect x="10" y="25" width="16" height="7" rx="2" fill="{c["accent"]}"/>'
        f'<path d="M11 38h10" stroke="{c["line"]}" stroke-width="2.5" stroke-linecap="round"/>'
    ),
    # onCreate: a lightning bolt on a page
    "oncreate": lambda c: (
        f'<rect x="6" y="6" width="36" height="36" rx="8" fill="{c["fill"]}" stroke="{c["line"]}" stroke-width="2.5"/>'
        f'<path d="M26 11l-10 15h8l-3 11 11-16h-8z" fill="{c["accent"]}" stroke="{c["accentLine"]}" stroke-width="1.5" stroke-linejoin="round"/>'
    ),
    # precedence: three stacked layers, the top one highlighted
    "layers": lambda c: (
        f'<path d="M24 30l18-8-18-8-18 8z" fill="{c["fill"]}" stroke="{c["line"]}" stroke-width="2.5" stroke-linejoin="round" transform="translate(0 8)"/>'
        f'<path d="M24 30l18-8-18-8-18 8z" fill="{c["fill"]}" stroke="{c["line"]}" stroke-width="2.5" stroke-linejoin="round"/>'
        f'<path d="M24 30l18-8-18-8-18 8z" fill="{c["accent"]}" stroke="{c["accentLine"]}" stroke-width="2" stroke-linejoin="round" transform="translate(0 -8)"/>'
    ),
    # typed templates: a class box with typed properties and a check
    "typed": lambda c: (
        f'<rect x="6" y="8" width="30" height="32" rx="4" fill="{c["fill"]}" stroke="{c["line"]}" stroke-width="2.5"/>'
        f'<path d="M6 17h30" stroke="{c["line"]}" stroke-width="2.5"/>'
        f'<path d="M11 25h14M11 32h10" stroke="{c["line"]}" stroke-width="2.5" stroke-linecap="round"/>'
        f'<circle cx="36" cy="36" r="9" fill="{c["accent"]}" stroke="{c["accentLine"]}" stroke-width="1.5"/>'
        f'<path d="M32 36l3 3 5-6" fill="none" stroke="{c["accentLine"]}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>'
    ),
    # one small class: a cube
    "cube": lambda c: (
        f'<path d="M24 5l17 9v20l-17 9-17-9V14z" fill="{c["fill"]}" stroke="{c["line"]}" stroke-width="2.5" stroke-linejoin="round"/>'
        f'<path d="M7 14l17 9 17-9M24 23v20" fill="none" stroke="{c["line"]}" stroke-width="2.5" stroke-linejoin="round"/>'
        f'<path d="M24 5l17 9-17 9-17-9z" fill="{c["accent"]}" stroke="{c["accentLine"]}" stroke-width="2" stroke-linejoin="round"/>'
    ),
}

ICON_THEMES = {
    "light": dict(fill="#f0fdfa", line="#0f766e", accent="#fbbf24", accentLine="#92400e"),
    "dark": dict(fill="#16302e", line="#5eead4", accent="#fbbf24", accentLine="#78350f"),
}


def icon(name, c):
    return f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48" aria-hidden="true">{ICONS[name](c)}</svg>\n'


LIFECYCLE_SK = [
    ("1", "Control::createTemplate()", "Presenter alebo komponent si od autowirovanej továrne", "na šablóny vyžiada novú šablónu."),
    ("2", "Latte TemplateFactory z Nette", "vytvorí šablónu, nastaví $user, $baseUrl, $basePath,", "$flashes, $control, $presenter a spustí svoj onCreate."),
    ("3", "zaregistrované premenné", "Priradí sa každá hodnota z registerVariable() a nahradí", "hodnotu s rovnakým názvom z kroku 2."),
    ("4", "handlery $onCreate", "Vaše callbacky bežia v poradí, v akom boli pridané, a môžu", "čítať, meniť alebo pridávať premenné a filtre."),
    ("5", "presenter / komponent", "action*(), render*() alebo render() komponentu priraďujú", "až po vytvorení šablóny, takže ich hodnoty vyhrávajú."),
]

LIFECYCLE_ARIA = {
    "en": "Creating a template: 1. a presenter or component calls createTemplate, 2. Nette's Latte template factory creates it with its default variables and onCreate, "
          "3. the registered variables are assigned, 4. the onCreate handlers of AutoVariableTemplateFactory run, 5. the presenter or component sets its own variables.",
    "sk": "Vytvorenie šablóny: 1. presenter alebo komponent zavolá createTemplate, 2. Latte továreň na šablóny z Nette ju vytvorí s predvolenými premennými a svojím onCreate, "
          "3. priradia sa zaregistrované premenné, 4. spustia sa handlery onCreate z AutoVariableTemplateFactory, 5. presenter alebo komponent nastaví vlastné premenné.",
}

LIFECYCLE = [
    ("1", "Control::createTemplate()", "A presenter or component asks the autowired", "template factory for a new template."),
    ("2", "Nette's Latte TemplateFactory", "creates the template, sets $user, $baseUrl, $basePath,", "$flashes, $control, $presenter and runs its onCreate."),
    ("3", "registered variables", "Every registerVariable() value is assigned, replacing", "a value of the same name from step 2."),
    ("4", "$onCreate handlers", "Your callbacks run in the order they were added and", "can read, change or add variables and filters."),
    ("5", "presenter / component", "action*(), render*() or a control's render() assign", "after the template exists, so their values win."),
]


def lifecycle(c, lang="en"):
    steps = LIFECYCLE_SK if lang == "sk" else LIFECYCLE
    out = [
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 520" width="640" height="520" role="img" '
        f'aria-label="{LIFECYCLE_ARIA[lang]}">',
    ]
    for i, (num, title, line1, line2) in enumerate(steps):
        y = 12 + i * 102
        ours = num in ("3", "4")
        box = c["factory"] if ours else c["card"]
        title_color = c["factoryText"] if ours else c["text"]
        body_color = c["factoryText"] if ours else c["muted"]
        out += [
            f'<rect x="80" y="{y}" width="548" height="80" rx="12" fill="{box}" stroke="{c["stroke"]}" stroke-width="1.5"/>',
            f'<circle cx="40" cy="{y+40}" r="22" fill="{c["chip"] if ours else c["title"]}" stroke="{c["stroke"]}" stroke-width="1.5"/>',
            f'<text x="40" y="{y+47}" text-anchor="middle" font-family="{SANS}" font-size="20" font-weight="700" fill="{c["chipText"] if ours else c["key"]}">{num}</text>',
            f'<text x="100" y="{y+30}" font-family="{MONO}" font-size="15" font-weight="700" fill="{title_color}">{title}</text>',
            f'<text x="100" y="{y+52}" font-family="{SANS}" font-size="13.5" fill="{body_color}">{line1.replace("$", "&#36;")}</text>',
            f'<text x="100" y="{y+70}" font-family="{SANS}" font-size="13.5" fill="{body_color}">{line2.replace("$", "&#36;")}</text>',
        ]
        if ours:
            out.append(f'<text x="612" y="{y+30}" text-anchor="end" font-family="{SANS}" font-size="11" font-weight="600" fill="#fbbf24">AutoVariableTemplateFactory</text>')
        if i < len(steps) - 1:
            out.append(f'<path d="M40 {y+64}v34" stroke="{c["arrow"]}" stroke-width="2.5" stroke-linecap="round"/>')
            out.append(f'<path d="M34 {y+92}l6 7 6-7" fill="none" stroke="{c["arrow"]}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>')
    out.append("</svg>")
    return "\n".join(out) + "\n"


PUBLIC = OUT.parent
for name, colors in THEMES.items():
    (OUT / f"hero-{name}.svg").write_text(svg(colors), encoding="utf-8")
    (PUBLIC / "diagrams" / f"lifecycle-{name}.svg").write_text(lifecycle(colors), encoding="utf-8")
    (OUT / f"hero-sk-{name}.svg").write_text(svg(colors, HERO_TEXT["sk"]), encoding="utf-8")
    (PUBLIC / "diagrams" / f"lifecycle-sk-{name}.svg").write_text(lifecycle(colors, "sk"), encoding="utf-8")
for name, colors in ICON_THEMES.items():
    for icon_name in ICONS:
        suffix = "" if name == "light" else "-dark"
        (PUBLIC / "icons" / f"{icon_name}{suffix}.svg").write_text(icon(icon_name, colors), encoding="utf-8")
print("illustrations, icons and diagrams written to public/")
