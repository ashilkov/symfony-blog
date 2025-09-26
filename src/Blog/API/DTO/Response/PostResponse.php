<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\DTO\Response;

use App\User\Domain\Model\User;
use Symfony\Component\Serializer\Annotation\Groups;

class PostResponse
{
    public function __construct(
        #[Groups(['post:read'])]
        public ?int $id,
        #[Groups(['post:read'])]
        public ?string $title,
        #[Groups(['post:read'])]
        public ?string $content,
        #[Groups(['post:read'])]
        public ?User $user,
        public ?BlogResponse $blog,
        #[Groups(['post:read'])]
        public ?string $createdAt,
    ) {
    }
}
