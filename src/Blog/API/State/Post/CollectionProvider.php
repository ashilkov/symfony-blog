<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\State\Post;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\ArrayPaginator;
use ApiPlatform\State\ProviderInterface;
use App\Blog\API\Hydrator\PostHydrator;
use App\Blog\Domain\Repository\PostRepositoryInterface;

readonly class CollectionProvider implements ProviderInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private PostHydrator $postHydrator,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $blogId = $uriVariables['blog_id'] ?? null;
        if ($blogId) {
            $posts = $this->postRepository->findBy(['blog' => $blogId]);
        } else {
            $posts = $this->postRepository->findBy([]);
        }

        /** @var \App\Blog\Domain\Model\Post[] $posts */
        $total = $this->postRepository->count([]);

        $items = array_map(fn ($post) => $this->postHydrator->hydrate($post), $posts);

        // Return a paginator to satisfy API Platform expectations
        return new ArrayPaginator($items, 0, $total);
    }
}
