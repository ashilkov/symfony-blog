<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\DTO\Response;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GraphQl\Mutation;
use App\Blog\API\GraphQL\UploadImageResolver;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    shortName: 'Image',
    operations: [],
    normalizationContext: ['groups' => ['upload:read']],
    denormalizationContext: ['groups' => ['upload:write']],
    graphQlOperations: [
        new Mutation(
            resolver: UploadImageResolver::class,
            args: [
                'file' => ['type' => 'Upload!'],
            ],
            description: 'Uploads a file',
            input: false,
            name: 'upload'
        ),
    ]
)]
readonly class UploadResult
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        #[Groups(['upload:read'])]
        public ?int $id = null,
        #[Groups(['upload:read'])]
        public ?string $url = null,
    ) {
    }
}
