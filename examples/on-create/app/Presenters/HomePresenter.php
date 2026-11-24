<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\PriceControl;
use Nette\Application\UI\Presenter;

final class HomePresenter extends Presenter
{
    public function renderDefault(): void
    {
        $this->template->total = 1234.5;
    }

    protected function createComponentPrice(): PriceControl
    {
        return new PriceControl(19.9);
    }
}
