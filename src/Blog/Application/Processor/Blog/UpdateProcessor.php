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
use App\Blog\API\Hydrator\BlogHydrator;
use App\Blog\API\Hydrator\BlogUserHydrator;
use App\Blog\API\Hydrator\PostHydrator;
use App\Blog\API\Resource\Blog as BlogResource;
use App\Blog\Application\Command\Blog\UpdateCommand;
use App\Blog\Application\Handler\Blog\UpdateHandler;

readonly class UpdateProcessor implements ProcessorInterface
{
    public function __construct(
        private UpdateHandler $handler,
        private BlogHydrator $blogHydrator,
        private PostHydrator $postHydrator,
        private BlogUserHydrator $blogUserHydrator,
    ) {
    }

    /**
     * @param BlogResource|object $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): BlogResource
    {
        // Resolve ID from either REST or GraphQL paths
        $id = $uriVariables['blog_id'] ?? $uriVariables['id'] ?? $data->id ?? null;
        if (null === $id) {
            throw new \InvalidArgumentException('Blog ID is required for update.');
        }

        $command = new UpdateCommand(
            id: (int) $id,
            name: $data->name ?? null,
            description: $data->description ?? null,
        );

        $blog = ($this->handler)($command);

        // Hydrate read model to return consistent projection after mutation
        $resource = $this->blogHydrator->hydrate($blog);
        $resource->posts = array_map(fn ($post) => $this->postHydrator->hydrate($post), $blog->getPosts()->toArray());
        $resource->blogUsers = array_map(
            fn ($blogUser) => $this->blogUserHydrator->hydrate($blogUser),
            $blog->getBlogUsers()->toArray()
        );

        return $resource;
    }
}
