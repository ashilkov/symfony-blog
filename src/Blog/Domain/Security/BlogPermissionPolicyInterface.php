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
use App\User\Domain\Model\User;

interface BlogPermissionPolicyInterface
{
    public function canCreatePost(User $user, Blog $blog): bool;

    public function canEditPost(User $user, Post $post): bool;

    public function canDeletePost(User $user, Post $post): bool;
}
