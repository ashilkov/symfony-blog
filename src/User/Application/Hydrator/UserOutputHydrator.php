<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\User\Application\Hydrator;

use App\User\API\DTO\Response\UserResponse;
use App\User\Domain\Model\User;

class UserOutputHydrator implements UserHydratorInterface
{
    public function hydrate(User $user): UserResponse
    {
        return new UserResponse(
            id: $user->getId(),
            username: $user->getUsername(),
            email: $user->getEmail(),
            fullname: $user->getFullName(),
        );
    }
}
