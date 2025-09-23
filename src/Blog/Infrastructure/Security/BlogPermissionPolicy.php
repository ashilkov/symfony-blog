<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Security;

use App\Blog\Domain\Model\Blog;
use App\Blog\Domain\Model\Post;
use App\Blog\Domain\Security\BlogPermissionPolicyInterface;
use App\Blog\Infrastructure\Repository\BlogUserRepository;
use App\User\Domain\Model\User;

readonly class BlogPermissionPolicy implements BlogPermissionPolicyInterface
{
    public function __construct(private BlogUserRepository $blogUserRepository)
    {
    }

    public function canCreatePost(User $user, Blog $blog): bool
    {
        $role = $this->getRole($user, $blog);

        return in_array($role, ['ROLE_AUTHOR', 'ROLE_EDITOR', 'ROLE_ADMIN'], true);
    }

    public function canEditPost(User $user, Post $post): bool
    {
        $blog = $post->getBlog();
        if (!$blog instanceof Blog) {
            return false;
        }

        $role = $this->getRole($user, $blog);
        if (\in_array($role, ['ROLE_EDITOR', 'ROLE_ADMIN'], true)) {
            return true;
        }

        // Allow authors to edit their own post
        return $post->getUser()?->getId() === $user->getId();
    }

    public function canDeletePost(User $user, Post $post): bool
    {
        $blog = $post->getBlog();
        if (!$blog instanceof Blog) {
            return false;
        }

        $role = $this->getRole($user, $blog);

        return \in_array($role, ['ROLE_EDITOR', 'ROLE_ADMIN'], true);
    }

    private function getRole(User $user, Blog $blog): ?string
    {
        $blogUser = $this->blogUserRepository->findOneBy([
            'user' => $user,
            'blog' => $blog,
        ]);

        return $blogUser?->getRole();
    }
}
