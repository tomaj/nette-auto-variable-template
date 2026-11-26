<?php

declare(strict_types=1);

namespace Tomaj\Latte\Test\Fixtures;

use Nette\Application\UI\Template;

/**
 * Takes any variable through __set(), without dynamic properties.
 */
final class MagicSetTemplate implements Template
{
    /** @var array<string, mixed> */
    public array $variables = [];

    public function __set(string $name, mixed $value): void
    {
        $this->variables[$name] = $value;
    }

    #[\Override]
    public function render(): void {}

    #[\Override]
    public function setFile(string $file): static
    {
        return $this;
    }

    #[\Override]
    public function getFile(): ?string
    {
        return null;
    }
}
