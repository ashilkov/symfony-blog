<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\User\Application\Handler;

use App\User\API\DTO\Response\UserResponse;
use App\User\Application\Hydrator\UserHydratorInterface;
use App\User\Domain\Model\User;
use App\User\Domain\Repository\UserRepositoryInterface;

readonly class UserInfoHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserHydratorInterface $userOutputHydrator,
    ) {
    }

    public function __invoke(string $identifier): ?UserResponse
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['username' => $identifier]);
        if ($user) {
            return $this->userOutputHydrator->hydrate($user);
        }

        return null;
    }
}
