<?php

declare(strict_types=1);

namespace App\Tests;

use App\Bootstrap;
use Nette\Application\IPresenterFactory;
use Nette\Application\Request;
use Nette\Application\Responses\TextResponse;
use Nette\Application\UI\Presenter;
use Nette\Application\UI\TemplateFactory;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use Tomaj\Latte\AutoVariableTemplateFactory;

final class TemplateVariablesTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = Bootstrap::boot();
    }

    public function testTheDecoratorIsTheAutowiredTemplateFactory(): void
    {
        $factory = $this->container->getByType(TemplateFactory::class);

        self::assertInstanceOf(AutoVariableTemplateFactory::class, $factory);
        self::assertSame(['appName' => 'Acme Shop', 'supportEmail' => 'help@acme.test'], $factory->getVariables());
    }

    public function testEveryNewTemplateHasTheVariables(): void
    {
        $template = $this->container->getByType(TemplateFactory::class)->createTemplate();

        self::assertSame('Acme Shop', $template->appName);
        self::assertSame('help@acme.test', $template->supportEmail);
    }

    public function testThePresenterRendersThem(): void
    {
        $presenter = $this->container->getByType(IPresenterFactory::class)->createPresenter('Home');
        self::assertInstanceOf(Presenter::class, $presenter);
        $presenter->autoCanonicalize = false;

        $response = $presenter->run(new Request('Home', 'GET', ['action' => 'default']));
        self::assertInstanceOf(TextResponse::class, $response);
        $html = (string) $response->getSource();

        self::assertStringContainsString('<title>Acme Shop</title>', $html);
        self::assertStringContainsString('<a href="mailto:help@acme.test">help@acme.test</a>', $html);
    }
}
