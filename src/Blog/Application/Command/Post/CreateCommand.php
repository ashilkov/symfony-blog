<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Command\Post;

class CreateCommand
{
    public function __construct(
        public ?string $title,
        public ?string $content,
        public ?int $blogId,
        public ?int $userId,
    ) {
    }
}
