<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\DTO\Response;

use Symfony\Component\Serializer\Annotation\Groups;

class GeneratedBlog
{
    public function __construct(
        #[Groups(['blog:read'])]
        public ?int $id = null,
        #[Groups(['blog:read'])]
        public ?string $name = null,
        #[Groups(['blog:read'])]
        public ?string $description = null,
    ) {
    }
}
