<?php
/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\Link;
use App\Blog\API\State\BlogUser\CollectionProvider as BlogUserCollectionProvider;
use App\Blog\API\State\BlogUser\ItemProvider as BlogUserItemProvider;
use App\User\Domain\Model\User;
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
                'userId' => new Link(fromClass: User::class, identifiers: ['id']),
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
        )
    ]
)]
class BlogUser
{
    // Regular fields
    #[Groups(['blog_user:read'])]
    public ?Blog $blog;

    #[Groups(['blog_user:read'])]
    public ?User $user;

    #[Groups(['blog_user:read', 'blog:read'])]
    public ?string $role;

    public function __construct(?string $role, ?Blog $blog = null, ?User $user = null, ?string $id = null, ?string $memberId = null)
    {
        $this->blog = $blog;
        $this->user = $user;
        $this->role = $role;
    }
}
