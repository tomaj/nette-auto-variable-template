<?php

declare(strict_types=1);

namespace Tomaj\Latte\Test;

use Nette\Application\UI\Control;
use Nette\Application\UI\Template;
use PHPUnit\Framework\TestCase;
use Tomaj\Latte\AutoVariableTemplateFactory;
use Tomaj\Latte\Test\Fixtures\InheritedDynamicTemplate;
use Tomaj\Latte\Test\Fixtures\MagicSetTemplate;
use Tomaj\Latte\Test\Fixtures\RecordingTemplateFactory;
use Tomaj\Latte\Test\Fixtures\StubTemplate;
use Tomaj\Latte\Test\Fixtures\TypedTemplate;

final class AutoVariableTemplateFactoryTest extends TestCase
{
    public function testReturnsTheTemplateOfTheInnerFactory(): void
    {
        $inner = new RecordingTemplateFactory();
        $factory = new AutoVariableTemplateFactory($inner);

        $template = $factory->createTemplate();

        self::assertSame([$template], $inner->created);
    }

    public function testPassesControlAndClassToTheInnerFactory(): void
    {
        $inner = new RecordingTemplateFactory();
        $factory = new AutoVariableTemplateFactory($inner);
        $control = new class extends Control {};

        $factory->createTemplate();
        $factory->createTemplate($control);
        $factory->createTemplate($control, StubTemplate::class);
        $factory->createTemplate(null, StubTemplate::class);

        self::assertSame([
            [null, null],
            [$control, null],
            [$control, StubTemplate::class],
            [null, StubTemplate::class],
        ], $inner->calls);
    }

    public function testAssignsRegisteredVariables(): void
    {
        $service = new \stdClass();
        $factory = new AutoVariableTemplateFactory(new RecordingTemplateFactory());
        $factory->registerVariable('appName', 'My Application');
        $factory->registerVariable('settings', $service);
        $factory->registerVariable('limit', 10);
        $factory->registerVariable('nothing', null);
        $factory->registerVariable('flags', ['a' => true]);

        $template = $factory->createTemplate();

        self::assertSame([
            'appName' => 'My Application',
            'settings' => $service,
            'limit' => 10,
            'nothing' => null,
            'flags' => ['a' => true],
        ], get_object_vars($template));
    }

    public function testTemplateWithoutRegisteredVariablesIsUnchanged(): void
    {
        $factory = new AutoVariableTemplateFactory(new RecordingTemplateFactory(['basePath' => '/app']));

        self::assertSame(['basePath' => '/app'], get_object_vars($factory->createTemplate()));
    }

    public function testRegisteredVariableOverridesTheInnerFactoryValue(): void
    {
        $factory = new AutoVariableTemplateFactory(new RecordingTemplateFactory(['basePath' => '/app', 'user' => 'inner']));
        $factory->registerVariable('user', 'registered');

        self::assertSame(['basePath' => '/app', 'user' => 'registered'], get_object_vars($factory->createTemplate()));
    }

    public function testRegisteringTheSameKeyAgainReplacesTheValue(): void
    {
        $factory = new AutoVariableTemplateFactory(new RecordingTemplateFactory());
        $factory->registerVariable('theme', 'light');
        $factory->registerVariable('theme', 'dark');

        self::assertSame(['theme' => 'dark'], $factory->getVariables());
        self::assertSame(['theme' => 'dark'], get_object_vars($factory->createTemplate()));
    }

    public function testEveryTemplateGetsTheVariablesRegisteredAtItsCreation(): void
    {
        $factory = new AutoVariableTemplateFactory(new RecordingTemplateFactory());
        $factory->registerVariable('first', 1);
        $before = $factory->createTemplate();
        $factory->registerVariable('second', 2);
        $after = $factory->createTemplate();

        self::assertNotSame($before, $after);
        self::assertSame(['first' => 1], get_object_vars($before));
        self::assertSame(['first' => 1, 'second' => 2], get_object_vars($after));
    }

    public function testRegisterVariableIsFluent(): void
    {
        $factory = new AutoVariableTemplateFactory(new RecordingTemplateFactory());

        self::assertSame($factory, $factory->registerVariable('a', 1));
        self::assertSame($factory, $factory->registerVariable('a', 1)->registerVariable('b', 2));
    }

    public function testGetVariablesReturnsTheRegisteredVariablesInOrder(): void
    {
        $factory = new AutoVariableTemplateFactory(new RecordingTemplateFactory());

        self::assertSame([], $factory->getVariables());

        $factory->registerVariable('b', 2)->registerVariable('a', 1);

        self::assertSame(['b' => 2, 'a' => 1], $factory->getVariables());
    }

    public function testOnCreateHandlersRunOncePerTemplateInOrder(): void
    {
        $inner = new RecordingTemplateFactory();
        $factory = new AutoVariableTemplateFactory($inner);
        $calls = [];
        $factory->onCreate[] = static function (Template $template) use (&$calls): void {
            $calls[] = ['first', $template];
        };
        $factory->onCreate[] = static function (Template $template) use (&$calls): void {
            $calls[] = ['second', $template];
        };

        $one = $factory->createTemplate();
        $two = $factory->createTemplate();

        self::assertSame([['first', $one], ['second', $one], ['first', $two], ['second', $two]], $calls);
    }

    public function testOnCreateRunsAfterTheVariablesAreAssigned(): void
    {
        $factory = new AutoVariableTemplateFactory(new RecordingTemplateFactory());
        $factory->registerVariable('appName', 'My Application');
        $seen = null;
        $factory->onCreate[] = static function (Template $template) use (&$seen): void {
            $seen = get_object_vars($template);
            $template->title = 'Set by onCreate'; // @phpstan-ignore property.notFound (templates take any variable)
            $template->appName = 'Changed by onCreate'; // @phpstan-ignore property.notFound (templates take any variable)
        };

        $template = $factory->createTemplate();

        self::assertSame(['appName' => 'My Application'], $seen);
        self::assertSame(['appName' => 'Changed by onCreate', 'title' => 'Set by onCreate'], get_object_vars($template));
    }

    public function testOnCreateAcceptsAnyCallable(): void
    {
        $factory = new AutoVariableTemplateFactory(new RecordingTemplateFactory());
        $listener = new class {
            public int $count = 0;

            public function templateCreated(Template $template): void
            {
                $this->count++;
            }
        };
        $factory->onCreate[] = $listener->templateCreated(...);
        $factory->onCreate[] = $listener->templateCreated(...);

        $factory->createTemplate();

        self::assertSame(2, $listener->count);
    }

    public function testWithoutOnCreateHandlersNothingElseHappens(): void
    {
        $inner = new RecordingTemplateFactory();
        $factory = new AutoVariableTemplateFactory($inner);

        self::assertSame([], $factory->onCreate);
        self::assertSame([], get_object_vars($factory->createTemplate()));
        self::assertCount(1, $inner->calls);
    }

    public function testClassWithItsOwnAllowDynamicPropertiesAttributeGetsEveryVariable(): void
    {
        $factory = $this->factoryWithVariables();

        $template = $this->createWithoutDeprecations($factory, StubTemplate::class);

        self::assertInstanceOf(StubTemplate::class, $template);
        self::assertSame(['limit' => 10, 'appName' => 'My Application'], get_object_vars($template));
    }

    public function testClassInheritingAllowDynamicPropertiesGetsEveryVariable(): void
    {
        $factory = $this->factoryWithVariables();

        $template = $this->createWithoutDeprecations($factory, InheritedDynamicTemplate::class);

        self::assertInstanceOf(InheritedDynamicTemplate::class, $template);
        self::assertSame(['limit' => 10, 'appName' => 'My Application'], get_object_vars($template));
    }

    public function testClassWithMagicSetGetsEveryVariable(): void
    {
        $factory = $this->factoryWithVariables();

        $template = $this->createWithoutDeprecations($factory, MagicSetTemplate::class);

        self::assertInstanceOf(MagicSetTemplate::class, $template);
        self::assertSame(['limit' => 10, 'appName' => 'My Application'], $template->variables);
    }

    public function testTypedClassGetsOnlyTheVariablesItDeclaresWithoutDeprecation(): void
    {
        $factory = $this->factoryWithVariables();

        $template = $this->createWithoutDeprecations($factory, TypedTemplate::class);

        self::assertInstanceOf(TypedTemplate::class, $template);
        self::assertSame(['appName' => 'My Application'], get_object_vars($template));
    }

    public function testTheCheckIsPerTemplateClass(): void
    {
        $factory = $this->factoryWithVariables();

        $typed = $this->createWithoutDeprecations($factory, TypedTemplate::class);
        $stub = $this->createWithoutDeprecations($factory, StubTemplate::class);
        $typedAgain = $this->createWithoutDeprecations($factory, TypedTemplate::class);
        $stubAgain = $this->createWithoutDeprecations($factory, StubTemplate::class);

        self::assertSame(['appName' => 'My Application'], get_object_vars($typed));
        self::assertSame(['appName' => 'My Application'], get_object_vars($typedAgain));
        self::assertSame(['limit' => 10, 'appName' => 'My Application'], get_object_vars($stub));
        self::assertSame(['limit' => 10, 'appName' => 'My Application'], get_object_vars($stubAgain));
    }

    public function testDeclaredPropertyOfAnIncompatibleTypeThrowsTypeError(): void
    {
        $factory = new AutoVariableTemplateFactory(new RecordingTemplateFactory());
        $factory->registerVariable('appName', 42);

        try {
            $factory->createTemplate(null, TypedTemplate::class);
            self::fail('TypeError expected');
        } catch (\TypeError $e) {
            self::assertSame('Cannot assign int to property ' . TypedTemplate::class . '::$appName of type string', $e->getMessage());
        }
    }

    private function factoryWithVariables(): AutoVariableTemplateFactory
    {
        $factory = new AutoVariableTemplateFactory(new RecordingTemplateFactory());
        $factory->registerVariable('limit', 10); // TypedTemplate does not declare it; the declared appName after it is still assigned
        $factory->registerVariable('appName', 'My Application');

        return $factory;
    }

    /**
     * @param class-string<Template> $class
     */
    private function createWithoutDeprecations(AutoVariableTemplateFactory $factory, string $class): Template
    {
        $errors = [];
        set_error_handler(static function (int $level, string $message) use (&$errors): bool {
            $errors[] = $message;
            return true;
        });
        try {
            $template = $factory->createTemplate(null, $class);
        } finally {
            restore_error_handler();
        }
        self::assertSame([], $errors);

        return $template;
    }
}
