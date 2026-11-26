<?php

declare(strict_types=1);

namespace Tomaj\Latte\Test\Fixtures;

use Nette\Application\UI\Template;

/**
 * A parent class with #[\AllowDynamicProperties]: its subclasses take any variable too.
 */
#[\AllowDynamicProperties]
abstract class DynamicBaseTemplate implements Template
{
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
