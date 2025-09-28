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
use App\Blog\API\Hydrator\BlogHydrator;
use App\Blog\API\Hydrator\PostHydrator;
use App\Blog\API\Resource\Post as PostResource;
use App\Blog\Application\Command\Post\UpdateCommand;
use App\Blog\Application\Handler\Post\UpdateHandler;

readonly class UpdateProcessor implements ProcessorInterface
{
    public function __construct(
        private UpdateHandler $handler,
        private BlogHydrator $blogHydrator,
        private PostHydrator $postHydrator,
    ) {
    }

    /**
     * @param PostResource|object $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): PostResource
    {
        // Resolve ID from either REST or GraphQL paths
        $id = $uriVariables['post_id'] ?? $uriVariables['id'] ?? $data->id ?? null;
        if (null === $id) {
            throw new \InvalidArgumentException('Post ID is required for update.');
        }

        $command = new UpdateCommand(
            id: (int) $id,
            title: $data->title,
            content: $data->content,
        );

        $post = ($this->handler)($command);

        $resource = $this->postHydrator->hydrate($post);
        $resource->blog = $this->blogHydrator->hydrate($post->getBlog());

        return $resource;
    }
}
