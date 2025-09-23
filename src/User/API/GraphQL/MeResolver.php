<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\User\API\GraphQL;

use ApiPlatform\Metadata\Exception\AccessDeniedException;
use App\User\API\DTO\UserOutput;
use App\User\Application\Handler\UserInfoHandler;
use GraphQL\Type\Definition\ResolveInfo;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class MeResolver
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UserInfoHandler $userInfoHandler,
    ) {
    }

    /**
     * @param array<string, mixed>      $args
     * @param array<string, mixed>|null $context
     */
    public function __invoke(mixed $root, array $args, ?array $context = null, ?ResolveInfo $info = null): ?UserOutput
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return null;
        }

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            throw new AccessDeniedException();
        }

        return ($this->userInfoHandler)($user->getUserIdentifier());
    }
}
