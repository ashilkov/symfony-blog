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
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\Put;
use App\Blog\API\DTO\Response\GeneratedPost;
use App\Blog\API\GraphQL\PostGenerateResolver;
use App\Blog\API\State\Post\CollectionProvider;
use App\Blog\API\State\Post\ItemProvider;
use App\Blog\Application\Processor\Post\CreateProcessor;
use App\Blog\Application\Processor\Post\DeleteProcessor;
use App\Blog\Application\Processor\Post\UpdateProcessor;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(provider: ItemProvider::class),
        new GetCollection(provider: CollectionProvider::class),
        new \ApiPlatform\Metadata\Post(processor: CreateProcessor::class),
        new Put(read: false, processor: UpdateProcessor::class),
        new Delete(processor: DeleteProcessor::class),
    ],
    normalizationContext: ['groups' => ['post:read'], 'iri_only' => false, 'enable_max_depth' => true],
    denormalizationContext: ['groups' => ['post:write']],
    graphQlOperations: [
        new QueryCollection(provider: CollectionProvider::class),
        new Query(provider: ItemProvider::class),
        new Mutation(
            securityPostDenormalize: "is_granted('BLOG_CREATE_POST', object)",
            name: 'create',
            processor: CreateProcessor::class
        ),
        new Mutation(
            security: 'is_granted("BLOG_EDIT_POST", object)',
            name: 'edit',
            processor: UpdateProcessor::class
        ),
        new Mutation(
            security: 'is_granted("BLOG_DELETE_POST", object)',
            name: 'delete',
            processor: DeleteProcessor::class
        ),
        new QueryCollection(
            description: 'Get all posts of subscribed blogs',
            security: 'is_granted("ROLE_USER")',
            name: 'subscribed',
        ),
        new Query(
            resolver: PostGenerateResolver::class,
            args: [
                'title' => ['type' => 'String'],
                'content' => ['type' => 'String'],
                'blogId' => ['type' => 'String!'],
            ],
            description: 'Generate post information',
            security: 'is_granted("ROLE_USER")',
            output: GeneratedPost::class,
            name: 'generate',
        ),
    ]
)]
class Post
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        #[Groups(['post:read'])]
        public ?int $id = null,
        #[Groups(['post:read', 'post:write', 'blog:read'])]
        public ?string $title = null,
        #[Groups(['post:read', 'post:write', 'blog:read'])]
        public ?string $content = null,
        #[Groups(['post:read', 'post:write', 'blog:read'])]
        public ?string $createdAt = null,
        #[Groups(['post:read', 'post:write', 'blog:read'])]
        public ?string $updatedAt = null,
        #[Groups(['post:read', 'post:write'])]
        public ?Blog $blog = null,
        #[Groups(['post:read'])]
        public ?string $author = null,
        #[Groups(['post:read'])]
        public array $allowedActions = [],
    ) {
    }
}
