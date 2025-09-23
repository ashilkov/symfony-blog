<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\User\API\DTO;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GraphQl\Query;
use App\User\API\GraphQL\MeResolver;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    shortName: 'User',
    operations: [],
    normalizationContext: ['groups' => ['user:read']],
    graphQlOperations: [
        new Query(
            resolver: MeResolver::class,
            args: [],
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            read: false,
            name: 'me'
        ),
    ]
)]
readonly class UserOutput
{
    public function __construct(
        #[Groups('user:read')]
        public string|int $id,
        #[Groups('user:read')]
        public string $username,
        #[Groups('user:read')]
        public ?string $email = null,
        #[Groups('user:read')]
        public ?string $fullname = null,
    ) {
    }
}
