<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Model;

use App\Blog\Domain\Enum\BlogUserRole;
use App\Blog\Domain\Value\Blog\BlogId;
use App\Blog\Domain\Value\Common\UserId;

class BlogUser implements EntityInterface
{
    public function __construct(
        private ?UserId $userId = null,
        private ?BlogUserRole $role = null,
        private ?BlogId $blogId = null,
    ) {
    }

    public function getBlogId(): ?BlogId
    {
        return $this->blogId;
    }

    public function attachToBlog(BlogId $blogId): self
    {
        $this->blogId = $blogId;

        return $this;
    }

    public function getUserId(): ?UserId
    {
        return $this->userId;
    }

    public function assignUser(UserId $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getRole(): ?BlogUserRole
    {
        return $this->role;
    }

    public function changeRole(BlogUserRole $role): self
    {
        if ($this->role !== $role) {
            $this->role = $role;
        }

        return $this;
    }
}
