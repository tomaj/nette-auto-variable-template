<?php

declare(strict_types=1);

// Starts the application in PHP's built-in web server (php -S, as in the docs), requests each URL
// and prints the HTML. PHP errors and deprecations are displayed in the page, so they show up in the
// output too. CI compares the output with expected-output.txt.

$urls = count($argv) > 1 ? array_slice($argv, 1) : ['/', '/home/about'];

// a free port on 127.0.0.1
$probe = stream_socket_server('tcp://127.0.0.1:0');
\assert($probe !== false);
$port = (int) substr((string) strrchr((string) stream_socket_get_name($probe, false), ':'), 1);
fclose($probe);

$server = proc_open(
    [PHP_BINARY, '-d', 'display_errors=1', '-d', 'error_reporting=-1', '-S', "127.0.0.1:$port", '-t', __DIR__ . '/www'],
    [1 => ['file', '/dev/null', 'w'], 2 => ['file', '/dev/null', 'w']],
    $pipes,
);
\assert(is_resource($server));
register_shutdown_function(static fn() => proc_terminate($server));

for ($i = 0; $i < 50 && @fsockopen('127.0.0.1', $port) === false; $i++) {
    usleep(100_000);
}

foreach ($urls as $url) {
    // the Host header makes $baseUrl the same on every run, whatever port the server got
    $context = stream_context_create(['http' => ['header' => "Host: localhost:8000\r\n", 'ignore_errors' => true, 'follow_location' => false]]);
    $html = @file_get_contents("http://127.0.0.1:$port$url", context: $context);
    $status = $http_response_header[0] ?? 'no response';
    if ($html === false || !str_contains($status, ' 200 ')) {
        fwrite(STDERR, "GET $url failed: $status\n" . (string) $html);
        exit(1);
    }
    echo "=== GET $url ===\n", $html, "\n";
}
