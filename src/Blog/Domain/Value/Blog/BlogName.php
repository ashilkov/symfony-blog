<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Value\Blog;

use App\Blog\Domain\Value\ValueInterface;

class BlogName implements ValueInterface
{
    public function __construct(private string $value)
    {
        $trimmed = trim($value);
        if ('' === $trimmed) {
            throw new \InvalidArgumentException('Blog name cannot be empty.');
        }
        if (mb_strlen($trimmed) > 200) {
            throw new \InvalidArgumentException('Blog name is too long (max 200 chars).');
        }
        $this->value = $trimmed;
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
