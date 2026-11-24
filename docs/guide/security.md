---
title: Security
description: What not to register as a template variable, escaping, and how to report a vulnerability in nette-auto-variable-template.
---

# Security

## Registered variables reach every template

Every template created through the factory gets every registered variable: your presenters and components, and also components and templates from third-party packages, error pages, e-mail templates and anything else that uses the autowired template factory. Treat a registered variable as public to all template code.

- **Do not register secrets**: passwords, API keys, tokens, or configuration arrays and services that contain them (`%database%`, a whole `%parameters%` section, a mailer configured with SMTP credentials). A template only has to print `{dump}` or `{$config|json}` by mistake, in debug mode or in a log, to leak them.
- **Register narrow services.** Register a small read-only object with the values templates need (`Settings` with `version` and `supportEmail` in the [examples](/examples/hello-world)) instead of the container, an entity manager or a service with methods that change data.
- **Per-user data is per request.** A service such as the current user or the cart is the same instance in every template of the request. That is fine in PHP-FPM, where every request has its own container. In a long-running process (RoadRunner, Swoole, a worker rendering e-mails for many users), make sure such a service is reset between requests or users, or compute the value in [`onCreate`](/guide/on-create).

## Escaping

The factory only assigns values. Latte escapes them when a template prints them, according to the context (`{$appName}` in HTML text, in an attribute, in JavaScript), exactly as for any other variable. Do not register pre-rendered HTML as a string and print it with `|noescape`; register the data and build the HTML in the template.

## Reporting a vulnerability

Report vulnerabilities in private, through [GitHub private vulnerability reporting](https://github.com/tomaj/nette-auto-variable-template/security/advisories/new), as described in the [security policy (SECURITY.md)](https://github.com/tomaj/nette-auto-variable-template/blob/master/SECURITY.md), which also lists the supported versions. Do not open a public issue.
