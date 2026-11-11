<?php

declare(strict_types = 1);

namespace Innmind\Frame\Parse;

use Innmind\Frame\{
    Parsed,
    Unparsed,
};
use Innmind\Immutable\Str;

/**
 * @psalm-immutable
 * @implements Implementation<Str>
 */
final class Chunk implements Implementation
{
    /**
     * @param int<1, max> $size
     */
    private function __construct(
        private int $size,
        private Str $buffer,
    ) {
    }

    #[\Override]
    public function __invoke(Unparsed $char): self|Parsed
    {
        if ($this->buffer->length() === $this->size) {
            return Parsed::of($this->buffer);
        }

        // Use of Str::prepend() to keep the encoding
        $buffer = $char->unwrap()->prepend($this->buffer);

        if ($buffer->length() === $this->size) {
            return Parsed::of($buffer);
        }

        return new self($this->size, $buffer);
    }

    /**
     * @psalm-pure
     *
     * @param int<1, max> $size
     */
    public static function of(int $size): self
    {
        return new self($size, Str::of(''));
    }
}
