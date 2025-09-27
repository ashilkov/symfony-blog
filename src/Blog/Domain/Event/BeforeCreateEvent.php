<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Event;

use App\Blog\Domain\Model\Blog;
use App\Blog\Domain\User\UserSummary;

readonly class BeforeCreateEvent
{
    public function __construct(
        public Blog $blog,
        public UserSummary $user,
    ) {
    }
}
