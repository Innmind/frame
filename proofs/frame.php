<?php

declare(strict_types = 1);

use Innmind\Frame\Frame;
use Innmind\Immutable\{
    Sequence,
    Str,
};
use Innmind\BlackBox\{
    Set,
    Prove,
    Runner\Assert,
};

return static function(Prove $prove) {
    yield $prove
        ->proof('Frame::chunk()')
        ->given(
            Set::integers()->between(1, 100),
            Set::sequence(
                Set::strings(),
            )->atLeast(1),
        )
        ->filter(static fn($size, $chunks) => \strlen(\implode('', $chunks)) >= $size)
        ->test(static function(Assert $assert, $size, $chunks) {
            $frame = Frame::chunk($size);
            $parsed = $frame(
                Sequence::of(...$chunks)->map(Str::of(...)),
            );

            $assert->same(1, $parsed->size());
            [$parsed] = $parsed->toList();
            $assert
                ->number($parsed->length())
                ->lessThanOrEqual($size);
            $assert
                ->string(\implode('', $chunks))
                ->startsWith($parsed->toString());
        });
};
