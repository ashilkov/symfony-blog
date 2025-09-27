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
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\Link;
use App\Blog\API\State\BlogUser\CollectionProvider as BlogUserCollectionProvider;
use App\Blog\API\State\BlogUser\ItemProvider as BlogUserItemProvider;
use App\Blog\Domain\Enum\BlogUserRole;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/blogs/{blogId}/members',
            uriVariables: [
                'blogId' => new Link(fromProperty: 'blogUsers', fromClass: Blog::class, identifiers: ['id']),
            ],
            provider: BlogUserCollectionProvider::class
        ),
        new Get(
            uriTemplate: '/blogs/{blogId}/members/{userId}',
            uriVariables: [
                'blogId' => new Link(fromProperty: 'blogUsers', fromClass: Blog::class, identifiers: ['id']),
                'userId' => new Link(fromProperty: 'userId', fromClass: BlogUser::class, identifiers: ['userId']),
            ],
            provider: BlogUserItemProvider::class
        ),
    ],
    normalizationContext: ['groups' => ['blog_user:read'], 'iri_only' => false],
    denormalizationContext: ['groups' => ['blog_user:write']],
    graphQlOperations: [
        new Query(
            provider: BlogUserItemProvider::class,
        ),
        new QueryCollection(
            provider: BlogUserCollectionProvider::class,
        ),
    ]
)]
class BlogUser
{
    // Regular fields
    #[ApiProperty(identifier: true)]
    #[Groups(['blog_user:read'])]
    public ?Blog $blog;

    #[ApiProperty(identifier: true)]
    #[Groups(['blog_user:read'])]
    public ?int $userId;

    #[Groups(['blog_user:read', 'blog:read'])]
    public ?BlogUserRole $role;

    public function __construct(?BlogUserRole $role = null, ?Blog $blog = null, ?int $userId = null)
    {
        $this->blog = $blog;
        $this->userId = $userId;
        $this->role = $role;
    }
}
