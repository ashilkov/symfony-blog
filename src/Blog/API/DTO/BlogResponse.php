<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\DTO;

use Symfony\Component\Serializer\Annotation\Groups;

class BlogResponse
{
    public function __construct(
        #[Groups(['blog:read'])]
        public ?int $id,
        #[Groups(['blog:read'])]
        public ?string $name,
        #[Groups(['blog:read'])]
        public ?string $description,
    ) {
    }
}
