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
            provider: BlogUserCollectionProvider::class,
        ),
        new Get(
            uriTemplate: '/blogs/{blogId}/members/{userId}',
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
    public function __construct(
        #[ApiProperty(identifier: true)]
        #[Groups(['blog_user:read'])]
        public ?int $blogId = null,
        #[ApiProperty(identifier: true)]
        #[Groups(['blog_user:read'])]
        public ?int $userId = null,
        #[Groups(['blog_user:read', 'blog:read'])]
        public ?BlogUserRole $role = null,
    ) {
    }
}
