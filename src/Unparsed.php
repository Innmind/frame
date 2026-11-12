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
        private Str $char,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     */
    public static function of(Str $char): self
    {
        return new self($char);
    }

    public function unwrap(): Str
    {
        return $this->char;
    }
}
