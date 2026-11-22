<?php

declare(strict_types=1);

namespace Tomaj\Latte\Test;

use Nette\Application\UI\Presenter;
use Nette\Application\UI\TemplateFactory;
use Nette\Bridges\ApplicationDI\LatteExtension;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Http\UrlScript;
use PHPUnit\Framework\TestCase;
use Tomaj\Latte\AutoVariableTemplateFactory;
use Tomaj\Latte\Test\Fixtures\AppTemplate;
use Tomaj\Latte\Test\Fixtures\Settings;
use Tomaj\Latte\Test\Fixtures\TemplateListener;
use Tomaj\Latte\Test\Fixtures\WidgetControl;

/**
 * The factory wired with Nette DI and Latte exactly as the README shows (tests/Fixtures/config.neon).
 */
final class NetteIntegrationTest extends TestCase
{
    private string $tempDir;

    private Container $container;

    #[\Override]
    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/nette-auto-variable-template-' . bin2hex(random_bytes(6));
        mkdir($this->tempDir);
        $tempDir = $this->tempDir;

        $class = new ContainerLoader($tempDir . '/di', true)->load(static function (Compiler $compiler) use ($tempDir): ?string {
            $compiler->addExtension('latte', new LatteExtension($tempDir . '/latte'));
            $compiler->loadConfig(__DIR__ . '/Fixtures/config.neon');

            return null;
        }, $tempDir);
        $this->container = new $class();
    }

    #[\Override]
    protected function tearDown(): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->tempDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );
        foreach ($files as $file) {
            \assert($file instanceof \SplFileInfo);
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($this->tempDir);
    }

    public function testTheFactoryIsTheAutowiredTemplateFactory(): void
    {
        $factory = $this->container->getByType(TemplateFactory::class);

        self::assertInstanceOf(AutoVariableTemplateFactory::class, $factory);
        self::assertSame($factory, $this->container->getService('templateFactory'));
        self::assertSame(['appName' => 'My Application', 'settings' => $this->container->getByType(Settings::class)], $factory->getVariables());
    }

    public function testRendersALatteTemplateWithTheRegisteredVariables(): void
    {
        $template = $this->factory()->createTemplate();
        self::assertInstanceOf(Template::class, $template);
        $template->setFile(__DIR__ . '/Fixtures/templates/page.latte');

        self::assertSame("<h1>My Application</h1>\n<p>Version: 4.0</p>\n<p>listener</p>\n", $template->renderToString());
    }

    public function testOnCreateHandlerFromTheConfigurationRunsForEveryTemplate(): void
    {
        $listener = $this->container->getByType(TemplateListener::class);
        $factory = $this->factory();

        $one = $factory->createTemplate();
        $two = $factory->createTemplate(new WidgetControl());

        self::assertSame([$one, $two], $listener->templates);
    }

    public function testComponentTemplatesGetTheVariables(): void
    {
        $presenter = new class extends Presenter {};
        $presenter->injectPrimary(new Request(new UrlScript('https://example.com/')), new Response(), templateFactory: $this->factory());
        $presenter->addComponent($control = new WidgetControl(), 'widget');

        ob_start();
        try {
            $control->render();
        } finally {
            $html = ob_get_clean();
        }

        self::assertSame("<div class=\"widget\">My Application 4.0 widget</div>\n", $html);
    }

    public function testCustomTemplateClassIsPassedThrough(): void
    {
        $template = $this->factory()->createTemplate(new WidgetControl(), AppTemplate::class);

        self::assertInstanceOf(AppTemplate::class, $template);
        self::assertSame('My Application', $template->appName);
        self::assertSame($this->container->getByType(Settings::class), $template->settings);
        self::assertSame('listener', $template->createdBy);
    }

    private function factory(): AutoVariableTemplateFactory
    {
        $factory = $this->container->getByType(TemplateFactory::class);
        self::assertInstanceOf(AutoVariableTemplateFactory::class, $factory);

        return $factory;
    }
}
