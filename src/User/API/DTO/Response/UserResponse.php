<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\User\API\DTO\Response;

use Symfony\Component\Serializer\Annotation\Groups;

readonly class UserResponse
{
    public function __construct(
        #[Groups('user:read')]
        public string|int $id,
        #[Groups('user:read')]
        public string $username,
        #[Groups('user:read')]
        public ?string $email = null,
        #[Groups('user:read')]
        public ?string $fullname = null,
    ) {
    }
}
