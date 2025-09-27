<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Command\Blog;

class CreateCommand
{
    public function __construct(
        public ?string $name,
        public ?string $description,
        public ?int $userId,
    ) {
    }
}
