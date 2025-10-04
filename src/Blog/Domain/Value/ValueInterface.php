<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Value;

interface ValueInterface
{
    public function value(): mixed;

    public function equals(ValueInterface $other): bool;

    public function __toString(): string;
}
