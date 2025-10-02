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
use App\Blog\Application\CurrentUserProviderInterface;
use App\Blog\Domain\Model\Blog;
use App\Blog\Domain\Repository\BlogRepositoryInterface;

readonly class CollectionProvider implements ProviderInterface
{
    public function __construct(
        private BlogHydrator $blogHydrator,
        private PostHydrator $postHydrator,
        private BlogUserHydrator $blogUserHydrator,
        private CurrentUserProviderInterface $userProvider,
        private BlogRepositoryInterface $blogRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $userId = $this->userProvider->getUserId();
        $blogs = $this->blogRepository->findWithSubscriptions($userId);

        $total = count($blogs);

        $items = array_map(function (array $row) {
            /** @var Blog $blog */
            $blog = $row['blog'];
            $subscribed = 1 === (int) $row['subscribed'];

            $blogResource = $this->blogHydrator->hydrate($blog);
            $blogResource->posts = array_map(
                fn ($post) => $this->postHydrator->hydrate($post),
                $blog->getPosts()
            );
            $blogResource->blogUsers = array_map(
                fn ($blogUser) => $this->blogUserHydrator->hydrate($blogUser),
                $blog->getBlogUsers()
            );
            $blogResource->subscribed = $subscribed;

            return $blogResource;
        }, $blogs);

        // Return a paginator to satisfy API Platform expectations
        return new ArrayPaginator($items, 0, $total);
    }
}
