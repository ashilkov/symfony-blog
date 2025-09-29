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
use ApiPlatform\Metadata\GraphQl\DeleteMutation;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Put;
use App\Blog\API\DTO\Response\Blog\GeneratedBlog;
use App\Blog\API\GraphQL\BlogGenerateResolver;
use App\Blog\API\State\Blog\CollectionProvider;
use App\Blog\API\State\Blog\ItemProvider;
use App\Blog\Application\Processor\Blog\CreateProcessor;
use App\Blog\Application\Processor\Blog\DeleteProcessor;
use App\Blog\Application\Processor\Blog\UpdateProcessor;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            uriVariables: ['blog_id' => new Link(fromClass: Blog::class, identifiers: ['id'])],
            provider: ItemProvider::class,
        ),
        new GetCollection(provider: CollectionProvider::class),
        new \ApiPlatform\Metadata\Post(processor: CreateProcessor::class),
        new Put(processor: UpdateProcessor::class),
        new Delete(processor: DeleteProcessor::class),
    ],
    normalizationContext: ['groups' => ['blog:read'], 'iri_only' => false, 'enable_max_depth' => true],
    denormalizationContext: ['groups' => ['blog:write']],
    graphQlOperations: [
        new Query(provider: ItemProvider::class),
        new QueryCollection(provider: CollectionProvider::class),
        new Mutation(
            security: 'is_granted("BLOG_CREATE_BLOG", object)',
            name: 'create',
            processor: CreateProcessor::class
        ),
        new Mutation(
            security: 'is_granted("BLOG_EDIT_BLOG", object)',
            name: 'update',
            processor: UpdateProcessor::class
        ),
        new DeleteMutation(
            security: 'is_granted("BLOG_DELETE_BLOG", object)',
            name: 'delete',
            processor: DeleteProcessor::class
        ),
        new Query(
            resolver: BlogGenerateResolver::class,
            args: ['name' => ['type' => 'String'], 'description' => ['type' => 'String']],
            description: 'Generate blog information',
            security: 'is_granted("ROLE_USER")',
            output: GeneratedBlog::class,
            read: false,
            name: 'generate'
        ),
    ],
)]
class Blog
{
    /** @var Post[] */
    #[Groups(['blog:read'])]
    #[ApiProperty(readableLink: true)]
    public array $posts = [];

    /** @var BlogUser[] */
    #[Groups(['blog:read'])]
    #[ApiProperty(readableLink: true)]
    public array $blogUsers = [];

    public function __construct(
        #[ApiProperty(identifier: true)]
        #[Groups(['blog:read', 'blog_user:read'])]
        public ?int $id = null,
        #[Groups(['blog:read', 'post:read', 'blog:write'])]
        public ?string $name = null,
        #[Groups(['blog:read', 'post:read', 'blog:write'])]
        public ?string $description = null,
        ?array $blogUsersData = null,
        ?array $postsData = null,
        #[Groups(['blog:read'])]
        public bool $subscribed = false,
    ) {
        $this->posts = $postsData ?? [];
        $this->blogUsers = $blogUsersData ?? [];
    }
}
