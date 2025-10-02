<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Command\Comment;

class CreateCommand
{
    public function __construct(
        public ?string $content,
        public ?int $postId,
        public ?int $userId,
    ) {
    }
}
