<?php

declare(strict_types=1);

namespace App\Components;

use Nette\Application\UI\Control;

final class MenuControl extends Control
{
    /**
     * @param array<string, string> $items label => presenter link
     */
    public function __construct(
        private readonly array $items,
    ) {}

    public function render(): void
    {
        // the control passes only its own data; $appName is already in the template
        $this->template->items = $this->items;
        $this->template->render(__DIR__ . '/menu.latte');
    }
}
