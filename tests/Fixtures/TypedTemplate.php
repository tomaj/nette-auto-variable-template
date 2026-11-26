<?php

declare(strict_types=1);

namespace Tomaj\Latte\Test\Fixtures;

use Nette\Application\UI\Template;

/**
 * A typed template class: declares $appName only and does not allow dynamic properties.
 */
final class TypedTemplate implements Template
{
    public string $appName = '';

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
