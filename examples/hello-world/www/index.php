<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$app = App\Bootstrap::boot()->getByType(Nette\Application\Application::class);
$app->onResponse[] = function ($a, $r) { fwrite(STDERR, 'DEBUG ' . get_class($r) . ' ' . ($r instanceof Nette\Application\Responses\RedirectResponse ? $r->getUrl() : '') . ' ' . json_encode($_SERVER['REQUEST_URI'] ?? null) . ' ' . (string) $a->getPresenter()?->getHttpRequest()->getUrl() . "\n"); };
$app->onError[] = function ($a, $e) { fwrite(STDERR, 'DEBUG ERR ' . $e . "\n"); };
$app->run();
fwrite(STDERR, "DEBUG end\n");
