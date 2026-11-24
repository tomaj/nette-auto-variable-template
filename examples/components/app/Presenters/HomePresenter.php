<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\CartControl;
use App\Components\MenuControl;
use Nette\Application\UI\Presenter;

final class HomePresenter extends Presenter
{
    protected function createComponentMenu(): MenuControl
    {
        return new MenuControl(['Home' => 'Home:', 'Products' => 'Home:products']);
    }

    protected function createComponentCart(): CartControl
    {
        return new CartControl(count: 3, total: 59.7);
    }
}
