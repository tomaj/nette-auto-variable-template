<?php

declare(strict_types=1);

namespace Tomaj\Latte\Test\Fixtures;

use Nette\Bridges\ApplicationLatte\Template;

/**
 * A custom Latte template class that declares none of the registered variables.
 */
final class PlainTemplate extends Template {}
