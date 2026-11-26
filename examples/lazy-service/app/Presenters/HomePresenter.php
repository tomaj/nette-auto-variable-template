<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\UI\Presenter;

final class HomePresenter extends Presenter
{
    public function renderDefault(): void
    {
        $this->template->title = 'Welcome';
    }

    public function renderRates(): void
    {
        $this->template->title = 'Exchange rates';
    }
}
