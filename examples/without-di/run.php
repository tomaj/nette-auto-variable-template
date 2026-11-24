<?php

declare(strict_types=1);

// AutoVariableTemplateFactory wired by hand: useful in a CLI script, a worker or a test,
// anywhere without a Nette DI container. php run.php

require __DIR__ . '/vendor/autoload.php';

use Latte\Engine;
use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\LatteFactory;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Bridges\ApplicationLatte\TemplateFactory;
use Tomaj\Latte\AutoVariableTemplateFactory;

// Nette's Latte template factory needs a factory for the Latte engine
$latteFactory = new class implements LatteFactory {
    public function create(?Control $control = null): Engine
    {
        $latte = new Engine();
        $latte->setTempDirectory(null); // compile in memory, no cache directory needed
        $latte->setStrictTypes();

        return $latte;
    }
};

$factory = new AutoVariableTemplateFactory(new TemplateFactory($latteFactory));
$factory->registerVariable('appName', 'Acme Shop')
    ->registerVariable('company', (object) ['name' => 'Acme s.r.o.', 'iban' => 'SK3112000000198742637541', 'email' => 'billing@acme.test']);

$factory->onCreate[] = static function (Template $template): void {
    $template->addFilter('iban', static fn(string $iban): string => trim(chunk_split($iban, 4, ' ')));
};

echo "Registered: ", implode(', ', array_keys($factory->getVariables())), "\n\n";

// every template created by the factory has appName and company
$invoice = $factory->createTemplate();
\assert($invoice instanceof Template);
echo $invoice->renderToString(__DIR__ . '/templates/invoice.latte', [
    'number' => '2026-0042',
    'issued' => new DateTimeImmutable('2026-11-24'),
]), "\n";

$email = $factory->createTemplate();
\assert($email instanceof Template);
$email->setFile(__DIR__ . '/templates/email.latte');
$email->name = 'Jane';
echo $email->renderToString();
