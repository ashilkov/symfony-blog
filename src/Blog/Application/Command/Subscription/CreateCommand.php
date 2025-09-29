<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Command\Subscription;

class CreateCommand
{
    public function __construct(
        public ?int $blogId,
        public ?int $subscriberId,
    ) {
    }
}
