---
title: How it works
description: What happens when a Nette template is created through AutoVariableTemplateFactory, and which value wins when Nette, the registered variables, onCreate and the presenter set the same variable.
---

# How it works

`AutoVariableTemplateFactory` is a [decorator](https://en.wikipedia.org/wiki/Decorator_pattern): it implements the same interface as Nette's template factory (`Nette\Application\UI\TemplateFactory`), receives Nette's factory in the constructor and adds one step to `createTemplate()`. The whole method is four lines:

<!-- snippet: skip -->
```php
// src/AutoVariableTemplateFactory.php, simplified
public function createTemplate(?Control $control = null, ?string $class = null): Template
{
    $template = $this->innerTemplateFactory->createTemplate($control, $class);
    $this->assignVariables($template);   // $template->$name = $value for every registered variable the template class can take
    foreach ($this->onCreate as $handler) {
        $handler($template);
    }
    return $template;
}
```

## What happens when a template is created

![Creating a template: a presenter or component calls createTemplate, Nette's Latte template factory creates it with its default variables and its own onCreate, the registered variables are assigned, the onCreate handlers run, and finally the presenter or component sets its own variables.](/diagrams/lifecycle-light.svg){.only-light}
![Creating a template: a presenter or component calls createTemplate, Nette's Latte template factory creates it with its default variables and its own onCreate, the registered variables are assigned, the onCreate handlers run, and finally the presenter or component sets its own variables.](/diagrams/lifecycle-dark.svg){.only-dark}

1. **A presenter or component needs its template.** `$this->template` is created on first use by `Control::createTemplate()`, which calls the autowired template factory with the control and the template class (`HomeTemplate` if such a class exists, see [Custom template classes](/guide/template-classes)).
2. **Nette's factory creates it.** `Nette\Bridges\ApplicationLatte\TemplateFactory` creates the Latte engine and the template object, sets `$user`, `$baseUrl`, `$basePath`, `$flashes`, `$control` and `$presenter` when the template class declares them, and calls its own `onCreate` handlers (the ones you add to `latte.templateFactory`).
3. **The registered variables are assigned**, in the order they were registered. A registered name that Nette already set in step 2 replaces Nette's value. A typed template class gets only the variables it declares ([Custom template classes](/guide/template-classes)).
4. **The `onCreate` handlers of `AutoVariableTemplateFactory` run**, in the order they were added. They see the registered variables and can change them or add new ones, filters and functions.
5. **The presenter or component sets its own variables** in `action*()`, `render*()` or its `render()` method. The template already exists at that point, so these assignments replace anything from the steps above.

Templates created outside a presenter, for example with `$templateFactory->createTemplate()` for an e-mail, go through steps 2 to 4; there is no control, so `$control` and `$presenter` are not set.

## Which value wins

The later step wins. The [precedence example](/examples/precedence) sets variables at every step and prints them:

<<< @/../examples/precedence/config/common.neon{neon}

<<< @/../examples/precedence/app/Templates/Stages.php{php}

<<< @/../examples/precedence/app/Presenters/HomePresenter.php{php}

Output:

<<< @/../examples/precedence/expected-output.txt{html}

| Variable | Set by | Value in the template | Why |
|---|---|---|---|
| `z` | Nette's `onCreate` only | `Nette onCreate` | Nothing later sets it. |
| `a` | Nette's `onCreate`, then registered | `registered` | Step 3 runs after step 2. |
| `b` | registered, then `onCreate` | `onCreate` | Step 4 runs after step 3. |
| `c` | registered, `onCreate`, presenter (`/` only) | `presenter` on `/`, `onCreate` on `/home/plain` | The presenter sets it after the template was created, but only in `renderDefault()`. |
| `theme` | registered twice | `dark` | The second `registerVariable('theme', ...)` replaced the first. |
| `basePath` | Nette, then registered | `/static` | A registered variable replaces Nette's default. |
| `baseUrl` | Nette only | `http://localhost:8000` | Not registered, so Nette's value stays. |

## What it does not do

- It does not touch templates that already exist. A variable registered while the application runs reaches only templates created after that.
- It does not render anything and does not know about Latte: it works with any `Nette\Application\UI\Template` the inner factory returns. Filters and functions are Latte features, so `onCreate` handlers check for `Nette\Bridges\ApplicationLatte\Template` before calling `addFilter()`.
- It does not copy or clone the values. Every template gets the same object for a service or an object value; a template that changes such an object changes it for the others too.
- Manual rendering with Latte's own engine (`$latte->render('file.latte', $params)`) does not use a template factory, so the variables are not there. Use `$templateFactory->createTemplate()` instead, as in the [without DI example](/examples/without-di).
