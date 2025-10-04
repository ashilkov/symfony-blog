<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Value\Blog;

use App\Blog\Domain\Value\ValueInterface;

class BlogDescription implements ValueInterface
{
    public function __construct(private string $value)
    {
        $normalized = trim(preg_replace('/\s+/', ' ', $value) ?? '');
        if ('' === $normalized) {
            throw new \InvalidArgumentException('Blog description cannot be empty.');
        }
        if (mb_strlen($normalized) > 2000) {
            throw new \InvalidArgumentException('Blog description is too long (max 2000 chars).');
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
}
