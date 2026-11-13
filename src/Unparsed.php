<?php

declare(strict_types = 1);

namespace Innmind\Frame;

use Innmind\Immutable\Str;

/**
 * @internal
 * @psalm-immutable
 */
final class Unparsed
{
    private function __construct(
        private Str $value,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     */
    public static function of(Str $value): self
    {
        return new self($value);
    }

    /**
     * @param pure-callable(Str): Str $map
     */
    public function map(callable $map): self
    {
        return new self($map($this->value));
    }

    public function unwrap(): Str
    {
        return $this->value;
    }
}
