<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Factory;

use App\Blog\Domain\Enum\BlogUserRole;
use App\Blog\Domain\Model\Blog;
use App\Blog\Domain\Model\BlogUser;
use App\Blog\Domain\Value\Common\UserId;

class BlogUserFactory
{
    public function create(UserId $userId, Blog $blog, BlogUserRole $role): BlogUser
    {
        return new BlogUser(userId: $userId, role: $role, blogId: $blog);
    }
}
