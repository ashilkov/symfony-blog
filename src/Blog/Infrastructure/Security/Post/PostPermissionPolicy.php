<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Security\Post;

use App\Blog\API\Resource\Blog;
use App\Blog\API\Resource\Post;
use App\Blog\Domain\Enum\BlogUserRole;
use App\Blog\Domain\Model\Post as PostDomain;
use App\Blog\Domain\Repository\BlogUserRepositoryInterface;
use App\Blog\Domain\Repository\PostRepositoryInterface;
use App\Blog\Domain\Security\PostPermissionPolicyInterface;

readonly class PostPermissionPolicy implements PostPermissionPolicyInterface
{
    public function __construct(
        private BlogUserRepositoryInterface $blogUserRepository,
        private PostRepositoryInterface $postRepository,
    ) {
    }

    public function canCreatePost(int $userId, Blog $blog): bool
    {
        $role = $this->getRole($userId, $blog);

        return in_array($role, [BlogUserRole::ROLE_AUTHOR, BlogUserRole::ROLE_EDITOR, BlogUserRole::ROLE_ADMIN], true);
    }

    public function canEditPost(int $userId, Post $post): bool
    {
        $blog = $post->blog;
        if (!$blog instanceof Blog) {
            return false;
        }

        $role = $this->getRole($userId, $blog);
        if (\in_array($role, [BlogUserRole::ROLE_EDITOR, BlogUserRole::ROLE_ADMIN], true)) {
            return true;
        }

        if (BlogUserRole::ROLE_AUTHOR === $role) {
            if (null === $post->id) {
                return false;
            }
            /** @var PostDomain $domainPost */
            $domainPost = $this->postRepository->findOneBy(['id' => $post->id]);
            if (!$domainPost) {
                return false;
            }

            return $domainPost->getUserId() === $userId;
        }

        return false;
    }

    public function canDeletePost(int $userId, Post $post): bool
    {
        $blog = $post->blog;
        if (!$blog instanceof Blog) {
            return false;
        }

        $role = $this->getRole($userId, $blog);

        return \in_array($role, [BlogUserRole::ROLE_EDITOR, BlogUserRole::ROLE_ADMIN], true);
    }

    private function getRole(int $userId, Blog $blog): ?BlogUserRole
    {
        $blogUser = $this->blogUserRepository->findOneBy([
            'userId' => $userId,
            'blog' => $blog->id,
        ]);

        return $blogUser?->getRole();
    }
}
