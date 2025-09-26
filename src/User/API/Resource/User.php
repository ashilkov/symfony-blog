<?php
/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\User\API\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\Post;
use App\User\API\DTO\Request\UserRequest;
use App\User\API\DTO\Response\UserResponse;
use App\User\API\GraphQL\MeResolver;
use App\User\Infrastructure\Persistence\UserRegistrationPersister;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    shortName: 'User',
    operations: [
        new Post(
            uriTemplate: '/users/',
            description: 'User registration',
            security: 'is_granted("PUBLIC_ACCESS")',
            input: UserRequest::class,
            name: 'user_registration',
            processor: UserRegistrationPersister::class,
        ),
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']],
    graphQlOperations: [
        new Query(
            resolver: MeResolver::class,
            args: [],
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            output: UserResponse::class,
            read: false,
            name: 'me'
        ),
    ]
)]
class User
{
    public function __construct(
        #[Groups(['user:read'])]
        public ?int $id,
        #[Groups(['user:read', 'post:read'])]
        public ?string $username,
        #[Groups(['user:read'])]
        public ?string $email,
        #[Groups(['user:read'])]
        public ?string $password,
        #[Groups(['user:read'])]
        public ?string $fullname,
        #[Groups(['user:read'])]
        public array $roles = [],
    )
    {
    }
}
