<?php

declare(strict_types = 1);

namespace Innmind\Frame\Parse;

use Innmind\Frame\{
    Parsed,
    Unparsed,
};

/**
 * @internal
 * @psalm-immutable
 * @template I
 * @template O
 * @implements Implementation<O>
 */
final class Map implements Implementation
{
    /**
     * @param Implementation<I> $parse
     * @param \Closure(I): O $map
     */
    private function __construct(
        private Implementation $parse,
        private \Closure $map,
    ) {
    }

    #[\Override]
    public function __invoke(Unparsed $char): self|Parsed
    {
        $result = ($this->parse)($char);

        if ($result instanceof Parsed) {
            return $result->map($this->map);
        }

        return new self($result, $this->map);
    }

    /**
     * @internal
     * @psalm-pure
     * @template A
     * @template B
     *
     * @param Implementation<A> $parse,
     * @param callable(A): B $map
     *
     * @return self<A, B>
     */
    public static function of(
        Implementation $parse,
        callable $map,
    ): self {
        return new self($parse, \Closure::fromCallable($map));
    }
}
