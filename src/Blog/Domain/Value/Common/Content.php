<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Value\Common;

use App\Blog\Domain\Value\ValueInterface;

class Content implements ValueInterface
{
    private string $value;

    public function __construct(string $value)
    {
        $normalized = self::normalize($value);

        // Base invariant(s) common to Post and Comment
        if ('' === $normalized) {
            throw new \InvalidArgumentException('Content cannot be empty.');
        }
        if (mb_strlen($normalized) > 10_000) {
            throw new \InvalidArgumentException('Content is too long (max 10,000 chars).');
        }

        $this->value = $normalized;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ValueInterface $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function normalize(string $value): string
    {
        // Trim and collapse excessive whitespace; adapt to your needs
        $v = trim($value);
        $v = preg_replace('/\R/u', "\n", $v) ?? $v; // normalize newlines

        // collapse spaces but keep newlines
        return preg_replace('/[^\S\n]+/u', ' ', $v) ?? $v;
    }
}
