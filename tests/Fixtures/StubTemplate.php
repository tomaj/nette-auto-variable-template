<?php

declare(strict_types=1);

namespace Tomaj\Latte\Test\Fixtures;

use Nette\Application\UI\Template;

/**
 * A template without Latte: variables are dynamic properties, as on Nette's Latte template.
 */
#[\AllowDynamicProperties]
final class StubTemplate implements Template
{
    private ?string $file = null;

    #[\Override]
    public function render(): void {}

    #[\Override]
    public function setFile(string $file): static
    {
        $this->file = $file;
        return $this;
    }

    #[\Override]
    public function getFile(): ?string
    {
        return $this->file;
    }
}
