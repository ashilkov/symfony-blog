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
use App\Blog\API\Hydrator\BlogHydrator;
use App\Blog\API\Hydrator\PostHydrator;
use App\Blog\Domain\Repository\PostRepositoryInterface;

readonly class CollectionProvider implements ProviderInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private BlogHydrator $blogHydrator,
        private PostHydrator $postHydrator,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Resolve pagination from context (defaults if not provided)
        $page = (int) ($context['filters']['page'] ?? 1);
        $itemsPerPage = (int) ($context['filters']['itemsPerPage'] ?? 30);
        $page = $page > 0 ? $page : 1;
        $itemsPerPage = $itemsPerPage > 0 ? $itemsPerPage : 30;
        $offset = ($page - 1) * $itemsPerPage;

        $blogId = $uriVariables['blog_id'] ?? null;
        if ($blogId) {
            $posts = $this->postRepository->findBy(['blog' => $blogId], null, $itemsPerPage, $offset);
        } else {
            $posts = $this->postRepository->findBy([], null, $itemsPerPage, $offset);
        }

        /** @var \App\Blog\Domain\Model\Post[] $posts */
        $total = $this->postRepository->count([]);

        $items = array_map(function ($post) {
            $postResource = $this->postHydrator->hydrate($post);
            $postResource->blog = $this->blogHydrator->hydrate($post->getBlog());

            return $postResource;
        }, $posts);

        // Return a paginator to satisfy API Platform expectations
        return new ArrayPaginator($items, 0, $total);
    }
}
