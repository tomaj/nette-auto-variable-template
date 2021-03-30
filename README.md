Nette auto variable template factory
====================================

Simple library that can assign variables in all latte templates (also components). Usefull when you need variable that will be always in all templates.


Instalation
-----------

```
composer require "tomaj/nette-auto-variable-template"
```

Usage
-----

In config.neon you need setup factory:

```
latte.templateFactory: {autowired: no}
templateFactory:
	factory: Tomaj\Latte\AutoVariableTemplateFactory(@latte.templateFactory)
	setup:
		- registerVariable('variableName', @someValue)
		- registerVariable('otherVariable', 'something else')
```

in each template will be variables *{$variableName}* and *{$otherVariable}*
