<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GraphQl\DeleteMutation;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use App\Blog\API\State\Subscription\ItemProvider;
use App\Blog\Application\Processor\Subscription\CreateProcessor;
use App\Blog\Application\Processor\Subscription\DeleteProcessor;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(provider: ItemProvider::class),
        new \ApiPlatform\Metadata\Post(processor: CreateProcessor::class),
        new Delete(processor: DeleteProcessor::class),
    ],
    normalizationContext: ['groups' => ['subscription:read']],
    denormalizationContext: ['groups' => ['subscription:write']],
    graphQlOperations: [
        new Query(provider: ItemProvider::class),
        new Mutation(name: 'create', processor: CreateProcessor::class),
        new DeleteMutation(
            args: ['blogId' => ['type' => 'Int!']],
            read: false,
            name: 'delete',
            processor: DeleteProcessor::class
        ),
    ]
)]
class Subscription
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        #[Groups(['subscription:read', 'subscription:write'])]
        public ?int $id = null,
        #[Groups(['subscription:read', 'subscription:write'])]
        public ?int $blogId = null,
        #[Groups(['subscription:read', 'subscription:write'])]
        public ?int $subscriberId = null,
    ) {
    }
}
