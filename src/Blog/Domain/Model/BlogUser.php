<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Model;

use App\Blog\Domain\Enum\BlogUserRole;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`blog_user`')]
class BlogUser
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Blog::class, inversedBy: 'blogUsers')]
    #[ORM\JoinColumn(name: 'blog_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Blog $blog;

    #[ORM\Id]
    #[ORM\Column(name: 'user_id', type: 'integer', nullable: false)]
    private ?int $userId;

    #[ORM\Column(enumType: BlogUserRole::class)]
    private BlogUserRole $role;

    public function __construct(Blog $blog, ?int $userId, BlogUserRole $role)
    {
        $this->blog = $blog;
        $this->userId = $userId;
        $this->role = $role;
    }

    public function getBlog(): Blog
    {
        return $this->blog;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getRole(): BlogUserRole
    {
        return $this->role;
    }

    public function setRole(BlogUserRole $role): void
    {
        $this->role = $role;
    }
}
