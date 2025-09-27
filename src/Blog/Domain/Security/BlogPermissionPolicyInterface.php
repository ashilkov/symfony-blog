<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Security;

use App\Blog\API\Resource\Blog;
use App\Blog\API\Resource\Post;

interface BlogPermissionPolicyInterface
{
    public function canCreatePost(int $userId, Blog $blog): bool;

    public function canEditPost(int $userId, Post $post): bool;

    public function canDeletePost(int $userId, Post $post): bool;
}
