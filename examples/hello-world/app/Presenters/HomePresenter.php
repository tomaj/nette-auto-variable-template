<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\UI\Presenter;

final class HomePresenter extends Presenter
{
    public function renderDefault(): void
    {
        // only the page-specific value; appName and settings come from the template factory
        $this->template->title = 'Welcome';
    }

    public function renderAbout(): void
    {
        $this->template->title = 'About us';
    }
}
