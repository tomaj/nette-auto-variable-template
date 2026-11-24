<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\UI\Presenter;

/**
 * @property-read HomeTemplate $template
 */
final class HomePresenter extends Presenter
{
    public function renderDefault(): void
    {
        $this->template->title = 'Typed template';
    }
}
