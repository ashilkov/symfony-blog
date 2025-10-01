<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Processor\Post;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Blog\API\Hydrator\PostHydrator;
use App\Blog\API\Resource\Post as PostResource;
use App\Blog\Application\Command\Post\CreateCommand;
use App\Blog\Application\CurrentUserProviderInterface;
use App\Blog\Application\Handler\Post\CreateHandler;

/**
 * @implements ProcessorInterface<PostResource, PostResource>
 */
readonly class CreateProcessor implements ProcessorInterface
{
    public function __construct(
        private CreateHandler $handler,
        private PostHydrator $postHydrator,
        private CurrentUserProviderInterface $userProvider,
    ) {
    }

    /**
     * @param PostResource|object $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): PostResource
    {
        $userId = $this->userProvider->getUserId();
        if (null === $userId) {
            throw new AccessDeniedException('You are not allowed to create a post.');
        }

        $command = new CreateCommand(
            title: $data->title,
            content: $data->content,
            blogId: $data->blog?->id,
            userId: $userId,
        );
        $post = ($this->handler)($command);

        return $this->postHydrator->hydrate($post);
    }
}
