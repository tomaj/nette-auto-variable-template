<?php

declare(strict_types=1);

// Runs the PHPUnit tests and prints the test names and the result, without the timing lines,
// so CI can compare the output with expected-output.txt.

exec(escapeshellarg(PHP_BINARY) . ' vendor/bin/phpunit --testdox --no-progress --colors=never 2>&1', $lines, $status);

foreach ($lines as $line) {
    if (preg_match('/^(PHPUnit |Runtime:|Configuration:|Time:)/', $line) !== 1) {
        echo rtrim($line), "\n";
    }
}

exit($status);
