<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\UI\Presenter;

final class HomePresenter extends Presenter
{
    public function renderDefault(): void
    {
        // 5. the presenter sets its variables last, after the template was created
        $this->template->c = 'presenter';
    }

    public function renderPlain(): void
    {
        // sets nothing
    }
}
