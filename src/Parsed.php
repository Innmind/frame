<?php

declare(strict_types = 1);

namespace Innmind\Frame;

use Innmind\Immutable\Str;

/**
 * @internal
 * @psalm-immutable
 * @template T
 */
final class Parsed
{
    /**
     * @param T $value
     */
    private function __construct(
        private mixed $value,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     * @template A
     *
     * @param A $value
     *
     * @return self<A>
     */
    public static function of(mixed $value): self
    {
        return new self($value);
    }

    /**
     * @return T
     */
    public function unwrap(): Str
    {
        return $this->value;
    }
}
