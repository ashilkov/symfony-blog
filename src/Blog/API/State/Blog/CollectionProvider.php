<?php
/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\State\Blog;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\ArrayPaginator;
use ApiPlatform\State\ProviderInterface;
use App\Blog\API\Hydrator\BlogHydrator;
use App\Blog\API\Hydrator\BlogUserHydrator;
use App\Blog\API\Hydrator\PostHydrator;
use App\Blog\Domain\Repository\BlogRepositoryInterface;

readonly class CollectionProvider implements ProviderInterface
{
    public function __construct(
        private BlogRepositoryInterface $blogRepository,
        private BlogHydrator            $blogHydrator,
        private PostHydrator            $postHydrator,
        private BlogUserHydrator        $blogUserHydrator,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {

        // Resolve pagination from context (defaults if not provided)
        $page = (int)($context['filters']['page'] ?? 1);
        $itemsPerPage = (int)($context['filters']['itemsPerPage'] ?? 30);
        $page = $page > 0 ? $page : 1;
        $itemsPerPage = $itemsPerPage > 0 ? $itemsPerPage : 30;
        $offset = ($page - 1) * $itemsPerPage;


        /** @var \App\Blog\Domain\Model\Blog[] $blogs */
        $blogs = $this->blogRepository->findBy([], null, $itemsPerPage, $offset);
        $total = $this->blogRepository->count([]);

        $items = array_map(function ($blog) {
            $blogResource = $this->blogHydrator->hydrate($blog);
            $blogResource->posts = array_map(
                fn($post) => $this->postHydrator->hydrate($post),
                $blog->getPosts()->toArray()
            );
            $blogResource->blogUsers = array_map(
                fn($blogUser) => $this->blogUserHydrator->hydrate($blogUser),
                $blog->getBlogUsers()->toArray()
            );

            return $blogResource;
        }, $blogs);

        // Return a paginator to satisfy API Platform expectations
        return new ArrayPaginator($items, 0, $total);
    }
}
