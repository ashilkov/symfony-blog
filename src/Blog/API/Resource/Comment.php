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
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use App\Blog\API\State\Comment\CollectionProvider;
use App\Blog\API\State\Comment\ItemProvider;
use App\Blog\Application\Processor\Comment\CreateProcessor;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(provider: ItemProvider::class),
        new GetCollection(provider: CollectionProvider::class),
        new \ApiPlatform\Metadata\Post(processor: CreateProcessor::class),
    ],
    normalizationContext: ['groups' => ['comment:read']],
    denormalizationContext: ['groups' => ['comment:write']],
    graphQlOperations: [
        new Query(provider: ItemProvider::class),
        new QueryCollection(provider: CollectionProvider::class),
        new Mutation(name: 'create', processor: CreateProcessor::class),
    ],
)]
class Comment
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        #[Groups(['comment:read', 'comment:write', 'post:read'])]
        public ?int $id = null,
        #[Groups(['comment:read', 'post:read'])]
        public ?string $author = null,
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
