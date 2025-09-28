<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Security;

use App\Blog\API\Resource\Blog;

interface BlogPermissionPolicyInterface
{
    public function canCreateBlog(int $userId): bool;

    public function canEditBlog(int $userId, Blog $blog): bool;

    public function canDeleteBlog(int $userId, Blog $blog): bool;
}
