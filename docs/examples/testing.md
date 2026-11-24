---
title: "Testing"
description: "PHPUnit tests that boot the application's container and check that the template factory is decorated, the variables are registered and a presenter renders them."
---

# Testing

<ExampleSource name="testing" />

The application's real configuration, booted in PHPUnit. The three tests check the wiring (the autowired factory is `AutoVariableTemplateFactory`), the registered values (`getVariables()`) and the result (a presenter renders them). See [Testing your integration](/guide/testing).

## Configuration

<<< @/../examples/testing/config/common.neon{neon}

`supportEmail` comes from a parameter, so a test environment could override it in its own config file.

## The tests

<<< @/../examples/testing/tests/TemplateVariablesTest.php{php}

<<< @/../examples/testing/phpunit.xml{xml}

`failOnDeprecation` makes the suite fail when a template class does not declare a registered variable ([Custom template classes](/guide/template-classes)).

## Output

`run.php` runs PHPUnit with `--testdox` and leaves out the lines with the PHPUnit version and the time:

<<< @/../examples/testing/run.php{php}

<<< @/../examples/testing/expected-output.txt{text}
