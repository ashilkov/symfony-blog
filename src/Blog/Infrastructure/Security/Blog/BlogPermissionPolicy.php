<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Security\Blog;

use App\Blog\API\Resource\Blog;
use App\Blog\Domain\Enum\BlogUserRole;
use App\Blog\Domain\Repository\BlogUserRepositoryInterface;
use App\Blog\Domain\Security\BlogPermissionPolicyInterface;
use App\Blog\Domain\User\UserReadModelPortInterface;

readonly class BlogPermissionPolicy implements BlogPermissionPolicyInterface
{
    public function __construct(
        private BlogUserRepositoryInterface $blogUserRepository,
        private UserReadModelPortInterface $userReadModelPort,
    ) {
    }

    public function canCreateBlog(int $userId): bool
    {
        $user = $this->userReadModelPort->findSummaryById($userId);
        if (null === $user) {
            return false;
        }

        return true;
    }

    public function canEditBlog(int $userId, Blog $blog): bool
    {
        $role = $this->getRole($userId, $blog);
        if (\in_array($role, [BlogUserRole::ROLE_EDITOR, BlogUserRole::ROLE_ADMIN], true)) {
            return true;
        }

        return false;
    }

    public function canDeleteBlog(int $userId, Blog $blog): bool
    {
        $role = $this->getRole($userId, $blog);

        return \in_array($role, [BlogUserRole::ROLE_ADMIN], true);
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
