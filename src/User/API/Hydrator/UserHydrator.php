<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\User\API\Hydrator;

use App\User\API\Resource\User as UserResource;
use App\User\Domain\Model\User;

class UserHydrator
{
    public function hydrate(User $user): UserResource
    {
        return new UserResource(
            id: $user->getId(),
            username: $user->getUsername(),
            email: $user->getEmail(),
            fullname: $user->getFullname(),
            roles: $user->getRoles(),
        );
    }
}
