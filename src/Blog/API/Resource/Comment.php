<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['comment:read']],
    denormalizationContext: ['groups' => ['comment:write']],
)]
class Comment
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        #[Groups(['comment:read', 'comment:write', 'post:read'])]
        public ?int $id = null,
        #[Groups(['comment:read', 'comment:write', 'post:read'])]
        public ?int $userId = null,
        #[Groups(['comment:read', 'comment:write'])]
        public ?int $postId = null,
        #[Groups(['comment:read', 'comment:write', 'post:read'])]
        public ?string $content = null,
        #[Groups(['comment:read', 'comment:write', 'post:read'])]
        public ?string $createdAt = null,
        #[Groups(['comment:read', 'comment:write', 'post:read'])]
        public ?string $updatedAt = null,
    ) {
    }
}
