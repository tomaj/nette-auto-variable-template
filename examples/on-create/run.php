<?php

declare(strict_types=1);

// Requests each URL from www/index.php in a fresh PHP process, as `php -S localhost:8000 -t www` would,
// and prints the HTML. CI compares the output with expected-output.txt.

$urls = count($argv) > 1 ? array_slice($argv, 1) : ['/'];

foreach ($urls as $url) {
    $env = ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => $url, 'SCRIPT_NAME' => '/index.php', 'HTTP_HOST' => 'localhost:8000'] + getenv();
    $process = proc_open([PHP_BINARY, '-d', 'display_errors=stderr', '-d', 'error_reporting=-1', __DIR__ . '/www/index.php'], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, __DIR__, $env);
    $html = (string) stream_get_contents($pipes[1]);
    $errors = (string) stream_get_contents($pipes[2]);
    $status = proc_close($process);
    if ($status !== 0 || $errors !== '' || $html === '') {
        fwrite(STDERR, "GET $url failed with status $status:\n$errors$html");
        exit(1);
    }
    echo "=== GET $url ===\n", $html, "\n";
}
