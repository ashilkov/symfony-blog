<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\User;

readonly class UserSummary
{
    public function __construct(
        public string $id,
        public string $email,
        public string $username,
    ) {
    }
}
