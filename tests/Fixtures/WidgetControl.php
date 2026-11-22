<?php

declare(strict_types=1);

namespace Tomaj\Latte\Test\Fixtures;

use Nette\Application\UI\Control;

/**
 * A component without a presenter, rendering tests/Fixtures/templates/widget.latte.
 */
final class WidgetControl extends Control
{
    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/templates/widget.latte');
        $this->template->render();
    }
}
