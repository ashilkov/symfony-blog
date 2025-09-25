<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\DTO;

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
    ) {
    }
}
