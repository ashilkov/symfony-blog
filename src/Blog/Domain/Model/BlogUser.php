<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Model;

use App\Blog\Domain\Enum\BlogUserRole;

class BlogUser implements EntityInterface
{
    public function __construct(
        private ?int $userId = null,
        private ?BlogUserRole $role = null,
        private ?Blog $blog = null,
    ) {
    }

    public function getBlog(): Blog
    {
        return $this->blog;
    }

    public function setBlog(Blog $blog): void
    {
        $this->blog = $blog;
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
