<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Stands for a service with an expensive constructor (a database query, a remote API, a CMS client).
 * Every run of the constructor appends a line to $logFile, so run.php can show when it ran.
 */
final class ExchangeRates
{
    /** @var array<string, float> */
    private array $rates;

    public function __construct(string $logFile)
    {
        file_put_contents($logFile, "ExchangeRates::__construct()\n", FILE_APPEND);
        $this->rates = ['EUR' => 1.0, 'CZK' => 24.35, 'USD' => 1.08];
    }

    public function rate(string $currency): float
    {
        return $this->rates[$currency];
    }
}
