<?php

declare(strict_types=1);

namespace Tomaj\Latte\Test;

use Latte\Engine;
use Latte\Loaders\StringLoader;
use Nette\Bridges\ApplicationLatte\UIExtension;
use Nette\Neon\Neon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Checks the code blocks of README.md, UPGRADE-4.0.md and docs/ against the current code.
 *
 * - A ```php block runs in its own PHP process with the Composer autoloader and must exit with status 0
 *   without writing to stderr. `$latteTemplateFactory` (Nette's Latte template factory), `$factory` (an
 *   AutoVariableTemplateFactory around it with `appName` registered) and `$template` (created by it) are
 *   already defined.
 * - A block directly after `<!-- snippet: skip -->` is not checked (a signature, or code that on purpose
 *   no longer works, such as the "before" side of an upgrade step).
 * - An ```html block directly after `<!-- snippet: output -->` is the output of the PHP block before it.
 *   Both are compared line by line, ignoring indentation and blank lines.
 * - A ```neon block must be valid NEON, and a ```latte block must compile with Nette's UI extension.
 */
final class DocsSnippetsTest extends TestCase
{
    private const string SKIP = '<!-- snippet: skip -->';
    private const string OUTPUT = '<!-- snippet: output -->';

    private const string PREPEND = <<<'PHP'
        <?php
        require %s;

        $latteTemplateFactory = new Nette\Bridges\ApplicationLatte\TemplateFactory(new class implements Nette\Bridges\ApplicationLatte\LatteFactory {
            public function create(?Nette\Application\UI\Control $control = null): Latte\Engine
            {
                return new Latte\Engine();
            }
        });
        $factory = new Tomaj\Latte\AutoVariableTemplateFactory($latteTemplateFactory);
        $factory->registerVariable('appName', 'My Application');
        $template = $factory->createTemplate();
        PHP;

    /**
     * @return iterable<string, array{string, string, ?string}> language, code, expected output
     */
    public static function snippetProvider(): iterable
    {
        $root = \dirname(__DIR__);
        $files = [$root . '/README.md', $root . '/UPGRADE-4.0.md'];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root . '/docs', \FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            \assert($file instanceof \SplFileInfo);
            $path = $file->getPathname();
            if ($file->getExtension() === 'md' && !str_contains($path, '/node_modules/') && !str_contains($path, '/.vitepress/')) {
                $files[] = $path;
            }
        }
        sort($files);

        foreach ($files as $path) {
            $relative = substr($path, \strlen($root) + 1);
            foreach (self::blocks((string) file_get_contents($path)) as $line => [$language, $code, $output]) {
                yield $relative . ':' . $line => [$language, $code, $output];
            }
        }
    }

    public function testDocumentationHasSnippets(): void
    {
        $languages = [];
        $outputs = 0;
        foreach (self::snippetProvider() as [$language, , $output]) {
            $languages[$language] = ($languages[$language] ?? 0) + 1;
            if ($output !== null) {
                $outputs++;
            }
        }

        self::assertGreaterThanOrEqual(6, $languages['php'] ?? 0);
        self::assertGreaterThanOrEqual(4, $languages['neon'] ?? 0);
        self::assertGreaterThanOrEqual(2, $languages['latte'] ?? 0);
        self::assertGreaterThanOrEqual(2, $outputs);
    }

    #[DataProvider('snippetProvider')]
    public function testSnippetIsValid(string $language, string $code, ?string $expectedOutput): void
    {
        match ($language) {
            'php' => $this->runPhp($code, $expectedOutput),
            'neon' => self::assertIsArray(Neon::decode($code)),
            'latte' => self::assertNotSame('', $this->latte()->compile($code)),
            default => self::fail('Unexpected language ' . $language),
        };
    }

    private function runPhp(string $code, ?string $expectedOutput): void
    {
        $script = tempnam(sys_get_temp_dir(), 'snippet');
        $prepend = tempnam(sys_get_temp_dir(), 'prepend');
        self::assertIsString($script);
        self::assertIsString($prepend);

        try {
            file_put_contents($script, "<?php\n\ndeclare(strict_types=1);\n\n" . $code);
            file_put_contents($prepend, \sprintf(self::PREPEND, var_export(__DIR__ . '/../vendor/autoload.php', true)));

            $process = proc_open(
                [PHP_BINARY, '-d', 'auto_prepend_file=' . $prepend, '-d', 'display_errors=stderr', '-d', 'error_reporting=-1', $script],
                [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
                $pipes,
            );
            self::assertIsResource($process);
            $stdout = (string) stream_get_contents($pipes[1]);
            $stderr = (string) stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $status = proc_close($process);
        } finally {
            unlink($script);
            unlink($prepend);
        }

        self::assertSame('', $stderr, "The snippet wrote to stderr:\n" . $code);
        self::assertSame(0, $status, "The snippet failed:\n" . $code . "\n" . $stdout);
        if ($expectedOutput !== null) {
            self::assertSame($this->normalize($expectedOutput), $this->normalize($stdout), "The documented output differs:\n" . $code);
        }
    }

    private function latte(): Engine
    {
        $latte = new Engine();
        $latte->setLoader(new StringLoader());
        $latte->addExtension(new UIExtension(null));

        return $latte;
    }

    /**
     * @return array<int, array{string, string, ?string}> line number => [language, code, expected output]
     */
    private static function blocks(string $markdown): array
    {
        $lines = explode("\n", $markdown);
        $blocks = [];
        $outputs = [];
        $lastPhp = null;
        $previous = '';
        $count = \count($lines);
        for ($i = 0; $i < $count; $i++) {
            if (preg_match('/^```(\w+)/', $lines[$i], $match) !== 1) {
                if (trim($lines[$i]) !== '') {
                    $previous = trim($lines[$i]);
                }
                continue;
            }
            $start = $i + 1;
            $body = [];
            for ($i++; $i < $count && !str_starts_with($lines[$i], '```'); $i++) {
                $body[] = $lines[$i];
            }
            $text = implode("\n", $body) . "\n";
            if ($match[1] === 'html' && $previous === self::OUTPUT) {
                self::assertNotNull($lastPhp, 'An output block must follow a PHP block (line ' . $start . ').');
                $outputs[$lastPhp] = $text;
            } elseif (\in_array($match[1], ['php', 'neon', 'latte'], true) && $previous !== self::SKIP) {
                $blocks[$start] = [$match[1], $match[1] === 'php' ? preg_replace('/^<\?php\s*/', '', $text) : $text];
                if ($match[1] === 'php') {
                    $lastPhp = $start;
                }
            }
            $previous = '```';
        }

        $result = [];
        foreach ($blocks as $line => [$language, $code]) {
            $result[$line] = [$language, $code, $outputs[$line] ?? null];
        }

        return $result;
    }

    private function normalize(string $html): string
    {
        $lines = array_filter(array_map(trim(...), explode("\n", $html)), static fn(string $line): bool => $line !== '');

        return implode("\n", $lines);
    }
}
