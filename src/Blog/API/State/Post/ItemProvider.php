<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\State\Post;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Blog\API\Hydrator\PostHydrator;
use App\Blog\API\Resource\Post;
use App\Blog\Domain\Repository\PostRepositoryInterface;
use App\Blog\Infrastructure\Security\Post\AllowedActionsResolver;

readonly class ItemProvider implements ProviderInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private PostHydrator $postHydrator,
        private AllowedActionsResolver $allowedActionsResolver,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Post
    {
        $id = $uriVariables['post_id'] ?? $uriVariables['id'] ?? null;
        if (null === $id) {
            return null;
        }

        /** @var \App\Blog\Domain\Model\Post $post */
        $post = $this->postRepository->findOneBy(['id' => (int) $id]);
        if (null === $post) {
            return null;
        }

        $postResource = $this->postHydrator->hydrate($post);
        $postResource->allowedActions = $this->allowedActionsResolver->resolve($postResource);

        return $postResource;
    }
}
