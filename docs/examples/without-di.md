---
title: "Without Nette DI"
description: "AutoVariableTemplateFactory wired by hand in a plain PHP script, around Nette's Latte template factory, rendering an invoice and an e-mail with the same registered variables."
---

# Without Nette DI

<ExampleSource name="without-di" />

No container, no presenter: a script creates Nette's Latte template factory by hand, wraps it, registers `appName` and `company`, adds an `iban` filter in `onCreate`, and renders two templates. Use this in a CLI command, a queue worker or a test.

## The script

<<< @/../examples/without-di/run.php{php}

## Templates

<<< @/../examples/without-di/templates/invoice.latte{latte}

<<< @/../examples/without-di/templates/email.latte{latte}

## Output

<<< @/../examples/without-di/expected-output.txt{text}

Both templates print `Acme Shop` and the company details; only `$number`, `$issued` and `$name` are passed for each template.
