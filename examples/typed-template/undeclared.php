<?php

declare(strict_types=1);

// What happens with a template class that does not declare a registered variable:
// the variable is not assigned to that template, and PHP reports nothing.

require __DIR__ . '/vendor/autoload.php';

use Nette\Application\UI\TemplateFactory;
use Nette\Bridges\ApplicationLatte\Template;

final class PlainTemplate extends Template {}

final class PartialTemplate extends Template
{
    public string $appName;
}

$factory = App\Bootstrap::boot()->getByType(TemplateFactory::class);

set_error_handler(static function (int $level, string $message): bool {
    echo $level === E_DEPRECATED ? 'Deprecated: ' : 'Error: ', $message, "\n";
    return true;
});

$plain = $factory->createTemplate(null, PlainTemplate::class);
$partial = $factory->createTemplate(null, PartialTemplate::class);
restore_error_handler();

echo 'PlainTemplate: ', json_encode(['appName' => isset($plain->appName), 'settings' => isset($plain->settings)]), "\n";
echo 'PartialTemplate: ', json_encode(['appName' => isset($partial->appName), 'settings' => isset($partial->settings)]), "\n";
echo 'PartialTemplate appName: ', $partial->appName, "\n";
