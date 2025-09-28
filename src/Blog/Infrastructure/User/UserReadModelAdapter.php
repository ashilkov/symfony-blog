<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\User;

use App\Blog\Domain\User\UserReadModelPortInterface;
use App\Blog\Domain\User\UserSummary;
use App\User\Domain\Model\User;
use App\User\Domain\Repository\UserRepositoryInterface;

readonly class UserReadModelAdapter implements UserReadModelPortInterface
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function findSummaryById(int $userId): ?UserSummary
    {
        /** @var User $user */
        $user = $this->userRepository->find($userId);
        if (null === $user) {
            return null;
        }

        return new UserSummary(
            id: (int) $user->getId(),
            email: (string) $user->getEmail(),
            username: (string) $user->getUsername(),
            roles: $user->getRoles(),
        );
    }
}
