<?php

declare(strict_types = 1);

namespace Innmind\Frame;

use Innmind\Immutable\Sequence;

/**
 * @internal
 * @psalm-immutable
 * @template T
 */
final class Parse
{
    /**
     * @param Parse\Implementation<T> $implementation
     */
    private function __construct(
        private Parse\Implementation $implementation,
    ) {
    }

    /**
     * @return Sequence<self<T>|Parsed<T>>
     */
    public function __invoke(Unparsed $char): Sequence
    {
        $result = ($this->implementation)($char);

        if ($result instanceof Parse\Implementation) {
            return Sequence::of(new self($result));
        }

        return Sequence::of($result);
    }

    /**
     * @internal
     * @psalm-pure
     * @template A
     *
     * @param Parse\Implementation<A> $implementation
     *
     * @return self<A>
     */
    public static function of(Parse\Implementation $implementation): self
    {
        return new self($implementation);
    }

    /**
     * @template U
     *
     * @param pure-callable(Parse\Implementation<T>): Parse\Implementation<U> $map
     *
     * @return self<U>
     */
    public function map(callable $map): self
    {
        return new self($map($this->implementation));
    }
}
