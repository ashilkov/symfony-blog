<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Processor\Blog;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Blog\API\Hydrator\BlogHydrator;
use App\Blog\API\Resource\Blog as BlogResource;
use App\Blog\Application\Command\Blog\CreateCommand;
use App\Blog\Application\CurrentUserProviderInterface;
use App\Blog\Application\Handler\Blog\CreateHandler;

readonly class CreateProcessor implements ProcessorInterface
{
    public function __construct(
        private CreateHandler $handler,
        private BlogHydrator $hydrator,
        private CurrentUserProviderInterface $userProvider,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): BlogResource
    {
        $userId = $this->userProvider->getUserId();
        if (null === $userId) {
            throw new AccessDeniedException('You are not allowed to create a blog.');
        }

        $command = new CreateCommand(
            name: $data->name ?? null,
            description: $data->description ?? null,
            userId: $userId
        );

        $blog = ($this->handler)($command);

        return $this->hydrator->hydrate($blog);
    }
}
