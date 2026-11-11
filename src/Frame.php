<?php

declare(strict_types = 1);

namespace Innmind\Frame;

use Innmind\Immutable\{
    Sequence,
    Str,
    Predicate\Instance,
};

/**
 * @psalm-immutable
 * @template-covariant T
 */
final class Frame
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
     * @return Sequence<T>
     */
    public function __invoke(Sequence $chunks): Sequence
    {
        /** @psalm-suppress InvalidArgument */
        return $chunks
            ->map(static fn($chunk) => $chunk->toEncoding(Str\Encoding::ascii))
            ->flatMap(static fn($chunk) => $chunk->chunk())
            ->map(Unparsed::of(...))
            ->prepend(Sequence::of($this->parse))
            ->aggregate(static fn(Parse|Parsed|Unparsed $a, Unparsed $b) => match (true) {
                $a instanceof Parse => $a($b),
                default => Sequence::of($a, $b),
            })
            ->takeWhile(static fn($value) => $value instanceof Parsed)
            ->keep(Instance::of(Parsed::class)) // for static analysis
            ->map(static fn($parsed): mixed => $parsed->unwrap());
    }

    /**
     * @psalm-pure
     *
     * @param int<1, max> $size
     */
    public static function chunk(int $size): self
    {
        return new self(Parse::of(
            Parse\Chunk::of($size),
        ));
    }
}
