<?php

declare(strict_types = 1);

namespace Innmind\Frame\Frame;

use Innmind\Frame\{
    Parse,
    Parsed,
    Unparsed,
};
use Innmind\Immutable\{
    Sequence,
    Str,
    Predicate\Instance,
};

/**
 * @psalm-immutable
 * @template-covariant T
 */
final class Passthrough
{
    /**
     * @param Parse<T> $parse
     */
    private function __construct(
        private Parse $parse,
    ) {
    }

    /**
     * @param Sequence<Str> $chunks
     *
     * @return Sequence<T|Str>
     */
    #[\NoDiscard]
    public function __invoke(Sequence $chunks): Sequence
    {
        /** @psalm-suppress InvalidArgument */
        return $chunks
            ->map(static fn($chunk) => $chunk->toEncoding(Str\Encoding::ascii))
            ->exclude(static fn($chunk) => $chunk->empty())
            ->map(Unparsed::of(...))
            ->prepend(Sequence::of($this->parse))
            ->aggregate(static fn(Parse|Parsed|Unparsed $a, Unparsed $b) => match (true) {
                $a instanceof Parse => $b
                    ->unwrap()
                    ->chunk()
                    ->map(Unparsed::of(...))
                    ->aggregate(static fn(Parse|Parsed|Unparsed $a, Unparsed $b) => match (true) {
                        $a instanceof Parse => $a($b),
                        default => Sequence::of($a, $b),
                    }),
                default => Sequence::of($a, $b),
            })
            ->keep(Instance::of(Parsed::class)->or(
                Instance::of(Unparsed::class),
            ))
            ->map(static fn($value): mixed => $value->unwrap());
    }

    /**
     * @internal
     * @psalm-pure
     * @template A
     *
     * @param Parse<A> $parse
     *
     * @return self<A>
     */
    public static function of(Parse $parse): self
    {
        return new self($parse);
    }
}
