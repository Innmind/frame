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

    yield $prove
        ->proof('Frame->map()')
        ->given(
            Set::integers()->between(1, 100),
            Set::sequence(
                Set::strings(),
            )->atLeast(1),
        )
        ->filter(static fn($size, $chunks) => \strlen(\implode('', $chunks)) >= $size)
        ->test(static function(Assert $assert, $size, $chunks) {
            $frame = Frame::chunk($size)->map(static fn($str) => [
                $str->length(),
                $str->encoding(),
            ]);
            $parsed = $frame(
                Sequence::of(...$chunks)->map(Str::of(...)),
            );

            $assert->same(1, $parsed->size());
            [[$parsed, $encoding]] = $parsed->toList();
            $assert
                ->number($parsed)
                ->lessThanOrEqual($size);
            $assert->same(Str\Encoding::ascii, $encoding);
        });

    yield $prove
        ->proof('Frame->flatMap()')
        ->given(
            Set::integers()->between(1, 100),
            Set::integers()->between(1, 100),
            Set::sequence(
                Set::strings()->madeOf(Set::strings()->chars()->alphanumerical()),
            )->atLeast(1),
        )
        ->filter(static fn($sizeA, $sizeB, $chunks) => \strlen(\implode('', $chunks)) >= ($sizeA + $sizeB))
        ->test(static function(Assert $assert, $sizeA, $sizeB, $chunks) {
            $frame = Frame::chunk($sizeA)->flatMap(
                static fn($a) => Frame::chunk($sizeB)->map(
                    static fn($b) => [$a, $b],
                ));
            $parsed = $frame(
                Sequence::of(...$chunks)->map(Str::of(...)),
            );

            $assert->same(1, $parsed->size());
            [[$a, $b]] = $parsed->toList();
            $assert->same($sizeA, $a->length());
            $assert
                ->number($b->length())
                ->lessThanOrEqual($sizeB);
            $assert
                ->string(\implode('', $chunks))
                ->startsWith($a->append($b)->toString());
        });
};
