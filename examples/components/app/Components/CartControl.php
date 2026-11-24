<?php

declare(strict_types=1);

namespace App\Components;

use Nette\Application\UI\Control;

final class CartControl extends Control
{
    public function __construct(
        private readonly int $count,
        private readonly float $total,
    ) {}

    public function render(): void
    {
        $this->template->count = $this->count;
        $this->template->total = $this->total;
        $this->template->render(__DIR__ . '/cart.latte');
    }
}
