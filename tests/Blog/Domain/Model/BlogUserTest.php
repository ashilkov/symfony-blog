<?php

declare(strict_types=1);

namespace App\Tests\Blog\Domain\Model;

use App\Blog\Domain\Enum\BlogUserRole;
use App\Blog\Domain\Model\BlogUser;
use App\Blog\Domain\Value\Blog\BlogId;
use App\Blog\Domain\Value\Common\UserId;
use PHPUnit\Framework\TestCase;

class BlogUserTest extends TestCase
{
    public function testCreateBlogUser(): void
    {
        $blogUser = new BlogUser();

        $this->assertNull($blogUser->getBlogId());
        $this->assertNull($blogUser->getUserId());
        $this->assertNull($blogUser->getRole());
    }

    public function testAttachToBlog(): void
    {
        $blogUser = new BlogUser();
        $blogId = new BlogId(1);
        $blogUser->attachToBlog($blogId);

        $this->assertSame($blogId, $blogUser->getBlogId());
    }

    public function testAssignUser(): void
    {
        $blogUser = new BlogUser();
        $userId = new UserId(1);
        $blogUser->assignUser($userId);

        $this->assertSame($userId, $blogUser->getUserId());
    }

    public function testChangeRole(): void
    {
        $blogUser = new BlogUser();

        $role = BlogUserRole::ROLE_AUTHOR;
        $blogUser->changeRole($role);

        $this->assertSame($role, $blogUser->getRole());
    }
}
