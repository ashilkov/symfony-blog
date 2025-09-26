<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Security;


use App\Blog\API\Resource\Blog;
use App\Blog\API\Resource\Post;
use App\Blog\Domain\Repository\BlogUserRepositoryInterface;
use App\Blog\Domain\Security\BlogPermissionPolicyInterface;
use App\User\Domain\Model\User;

readonly class BlogPermissionPolicy implements BlogPermissionPolicyInterface
{
    public function __construct(private BlogUserRepositoryInterface $blogUserRepository)
    {
    }

    public function canCreatePost(User $user, Blog $blog): bool
    {
        $role = $this->getRole($user, $blog);

        return in_array($role, ['ROLE_AUTHOR', 'ROLE_EDITOR', 'ROLE_ADMIN'], true);
    }

    public function canEditPost(User $user, Post $post): bool
    {
        $blog = $post->blog;
        if (!$blog instanceof Blog) {
            return false;
        }

        $role = $this->getRole($user, $blog);
        if (\in_array($role, ['ROLE_EDITOR', 'ROLE_ADMIN'], true)) {
            return true;
        }

        // Allow authors to edit their own post
        return $post->author?->getId() === $user->getId();
    }

    public function canDeletePost(User $user, Post $post): bool
    {
        $blog = $post->blog;
        if (!$blog instanceof Blog) {
            return false;
        }

        $role = $this->getRole($user, $blog);

        return \in_array($role, ['ROLE_EDITOR', 'ROLE_ADMIN'], true);
    }

    private function getRole(User $user, Blog $blog): ?string
    {
        $blogUser = $this->blogUserRepository->findOneBy([
            'user' => $user->getId(),
            'blog' => $blog->id,
        ]);

        return $blogUser?->getRole();
    }
}
