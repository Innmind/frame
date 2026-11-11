<?php

declare(strict_types = 1);

namespace Innmind\Frame\Parse;

use Innmind\Frame\{
    Frame,
    Unparsed,
};

/**
 * @internal
 * @psalm-immutable
 * @template I
 * @template O
 * @implements Implementation<O>
 */
final class FlatMap implements Implementation
{
    /**
     * @param Implementation<I> $parse
     * @param \Closure(I): Frame<O> $map
     * @param pure-Closure(Frame<O>): Implementation<O> $exfiltrate
     */
    private function __construct(
        private Implementation $parse,
        private \Closure $map,
        private \Closure $exfiltrate,
    ) {
    }

    #[\Override]
    public function __invoke(Unparsed $char): Implementation
    {
        $result = ($this->parse)($char);

        if ($result instanceof Implementation) {
            return new self(
                $result,
                $this->map,
                $this->exfiltrate,
            );
        }

        /** @psalm-suppress ImpureFunctionCall */
        return ($this->exfiltrate)(
            ($this->map)($result->unwrap()),
        );
    }

    /**
     * @internal
     * @psalm-pure
     * @template A
     * @template B
     *
     * @param Implementation<A> $parse,
     * @param callable(A): Frame<B> $map
     * @param pure-Closure(Frame<B>): Implementation<B> $exfiltrate
     *
     * @return self<A, B>
     */
    public static function of(
        Implementation $parse,
        callable $map,
        \Closure $exfiltrate,
    ): self {
        return new self(
            $parse,
            \Closure::fromCallable($map),
            $exfiltrate,
        );
    }
}
