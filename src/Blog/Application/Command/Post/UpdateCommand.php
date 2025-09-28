<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Command\Post;

class UpdateCommand
{
    public function __construct(
        public int $id,
        public ?string $title,
        public ?string $content,
    ) {
    }
}
