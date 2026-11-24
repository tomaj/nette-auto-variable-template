<?php

declare(strict_types=1);

// What happens with a template class that does not declare a registered variable:
// PHP reports every assignment of an undeclared property as deprecated.

require __DIR__ . '/vendor/autoload.php';

use Nette\Application\UI\TemplateFactory;
use Nette\Bridges\ApplicationLatte\Template;

final class PlainTemplate extends Template {}

$factory = App\Bootstrap::boot()->getByType(TemplateFactory::class);

set_error_handler(static function (int $level, string $message): bool {
    echo $level === E_DEPRECATED ? 'Deprecated: ' : 'Error: ', $message, "\n";
    return true;
});

$template = $factory->createTemplate(null, PlainTemplate::class);
restore_error_handler();

echo 'appName is still assigned: ', $template->appName, "\n";
