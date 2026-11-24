---
title: "Which value wins"
description: "The same variables set by Nette's onCreate, registerVariable, the factory's onCreate and a presenter: the example prints which value each template ends up with."
---

# Which value wins

<ExampleSource name="precedence" />

This example sets variables at every step of [creating a template](/guide/how-it-works) and prints them, so you can see which value wins: the later step.

## Configuration

<<< @/../examples/precedence/config/common.neon{neon}

## The handlers

<<< @/../examples/precedence/app/Templates/Stages.php{php}

## Presenter and template

<<< @/../examples/precedence/app/Presenters/HomePresenter.php{php}

<<< @/../examples/precedence/app/Presenters/templates/Home/default.latte{latte}

`plain.latte` is the same template for the `plain` action, whose `renderPlain()` sets nothing.

## Output

<<< @/../examples/precedence/expected-output.txt{html}

- `z`: set only by the `onCreate` of Nette's `latte.templateFactory`, so it keeps that value.
- `a`: Nette's `onCreate` runs inside the inner factory, before the registered variables are assigned, so `registered` wins.
- `b`: the factory's `onCreate` runs after the registered variables, so `onCreate` wins.
- `c`: on `/`, `renderDefault()` sets it after the template exists, so `presenter` wins; on `/home/plain` nothing does, so it stays `onCreate`.
- `theme`: registered twice; the second registration replaced the first.
- `basePath`: Nette sets it to the base path of the request (empty here), and the registered `/static` replaces it in every template. `baseUrl` is not registered, so Nette's value stays.
