<?php

declare(strict_types=1);

namespace App\Components;

use Nette\Application\UI\Control;

final class PriceControl extends Control
{
    public function __construct(
        private readonly float $price,
    ) {}

    public function render(): void
    {
        $this->template->price = $this->price;
        $this->template->render(__DIR__ . '/price.latte');
    }
}
