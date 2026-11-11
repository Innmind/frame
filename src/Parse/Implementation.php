<?php

declare(strict_types = 1);

namespace Innmind\Frame\Parse;

use Innmind\Frame\{
    Parsed,
    Unparsed,
};

/**
 * @psalm-immutable
 * @template T
 */
interface Implementation
{
    /**
     * @return self<T>|Parsed<T>
     */
    public function __invoke(Unparsed $char): self|Parsed;
}
