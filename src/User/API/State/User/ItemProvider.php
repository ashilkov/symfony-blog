<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\User\API\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\User\API\Hydrator\UserHydrator;
use App\User\API\Resource\User;
use App\User\Domain\Repository\UserRepositoryInterface;

readonly class ItemProvider implements ProviderInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserHydrator $userHydrator,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?User
    {
        $id = $uriVariables['user_id'] ?? null;
        if (null === $id) {
            return null;
        }

        /** @var \App\Blog\Domain\Model\Blog $blog */
        $user = $this->userRepository->findOneBy(['id' => (int) $id]);
        if (null === $user) {
            return null;
        }

        return $this->userHydrator->hydrate($user);
    }
}
