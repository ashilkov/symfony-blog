<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Processor\Comment;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Blog\API\Hydrator\CommentHydrator;
use App\Blog\Application\Command\Comment\CreateCommand;
use App\Blog\Application\CurrentUserProviderInterface;
use App\Blog\Application\Handler\Comment\CreateHandler;

class CreateProcessor implements ProcessorInterface
{
    public function __construct(
        private CurrentUserProviderInterface $userProvider,
        private CreateHandler $handler,
        private CommentHydrator $hydrator,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $userId = $this->userProvider->getUserId();
        if (null === $userId) {
            throw new \Exception('You are not allowed to create a comment.');
        }

        $command = new CreateCommand(
            content: $data->content ?? null,
            postId: $data->postId ?? null,
            userId: $userId,
        );

        $blog = ($this->handler)($command);

        return $this->hydrator->hydrate($blog);
    }
}
