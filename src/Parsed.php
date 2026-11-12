<?php

declare(strict_types = 1);

namespace Innmind\Frame;

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
     * @template U
     *
     * @param callable(T): U $map
     *
     * @return self<U>
     */
    public function map(callable $map): self
    {
        /** @psalm-suppress ImpureFunctionCall */
        return new self($map($this->value));
    }

    /**
     * @return T
     */
    public function unwrap(): mixed
    {
        return $this->value;
    }
}
