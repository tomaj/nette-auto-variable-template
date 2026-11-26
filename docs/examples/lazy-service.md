---
title: "Lazy service: constructed only when used"
description: "A registered ExchangeRates service marked lazy: true. Its constructor does not run on a page that never prints the variable, and runs once on the page that does."
---

# Lazy service: constructed only when used

<ExampleSource name="lazy-service" />

Every template gets `$rates`, an `ExchangeRates` service with a constructor that stands for an expensive one: it writes a line to `temp/exchange-rates.log` each time it runs. The service is marked `lazy: true` (PHP 8.4, nette/di 3.2.4+), so the template factory registers a lazy ghost and the constructor runs only when a template first calls a method that reads the object's state. `run.php` prints how many times the constructor has run after each page.

## Configuration

<<< @/../examples/lazy-service/config/common.neon{neon}

## The service

<<< @/../examples/lazy-service/app/Model/ExchangeRates.php{php}

## Templates

<<< @/../examples/lazy-service/app/Presenters/templates/@layout.latte{latte}

<<< @/../examples/lazy-service/app/Presenters/templates/Home/default.latte{latte}

<<< @/../examples/lazy-service/app/Presenters/templates/Home/rates.latte{latte}

## Output

<<< @/../examples/lazy-service/expected-output.txt{html}

The home page renders with `$rates` assigned, and the constructor has still run 0 times. The rates page calls `$rates->rate()`, which runs the constructor once. With `lazy: false` the count is 1 after the home page already. More in [Expensive services: `lazy: true`](/guide/configuration#expensive-services-lazy-true).
