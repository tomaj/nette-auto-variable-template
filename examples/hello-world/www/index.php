<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

App\Bootstrap::boot()
    ->getByType(Nette\Application\Application::class)
    ->run();
