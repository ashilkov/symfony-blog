<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Model;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\User\Domain\Model\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: '`blog_user`')]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/blogs/{blogId}/members/{userId}',
            uriVariables: [
                'blogId' => new Link(fromProperty: 'blogUsers', fromClass: Blog::class, identifiers: ['id']),
                'userId' => new Link(fromClass: User::class, identifiers: ['id']),
            ]
        ),
    ],
    normalizationContext: ['groups' => ['blog_user:read'], 'iri_only' => false],
    denormalizationContext: ['groups' => ['blog_user:write']],
    graphQlOperations: []
)]
class BlogUser
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Blog::class, inversedBy: 'blogUsers')]
    #[ORM\JoinColumn(name: 'blog_id', referencedColumnName: 'id', nullable: false)]
    #[ApiProperty(identifier: true)]
    private Blog $blog;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    #[ApiProperty(identifier: true)]
    private User $user;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(['blog_user:read'])]
    private string $role;

    public function __construct(Blog $blog, User $user, string $role)
    {
        $this->blog = $blog;
        $this->user = $user;
        $this->role = $role;
    }

    public function getBlog(): Blog
    {
        return $this->blog;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }
}
