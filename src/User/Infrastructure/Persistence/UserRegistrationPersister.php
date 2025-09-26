<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\User\Infrastructure\Persistence;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\User\API\DTO\Request\UserRequest;
use App\User\Domain\Enum\UserRole;
use App\User\Domain\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @implements ProcessorInterface<UserRequest, User>
 */
readonly class UserRegistrationPersister implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $user = new User();
        $user->setEmail($data->email);
        $user->setUsername($data->username);
        $user->setFullName($data->fullname);

        $password = $this->passwordHasher->hashPassword($user, $data->password);
        $user->setPassword($password);

        $user->addRole(UserRole::ROLE_USER);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
