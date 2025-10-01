<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\State\Blog;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Blog\API\Hydrator\BlogHydrator;
use App\Blog\API\Hydrator\BlogUserHydrator;
use App\Blog\API\Hydrator\PostHydrator;
use App\Blog\API\Resource\Blog;
use App\Blog\Domain\Repository\BlogRepositoryInterface;

readonly class ItemProvider implements ProviderInterface
{
    public function __construct(
        private BlogRepositoryInterface $blogRepository,
        private BlogHydrator $blogHydrator,
        private PostHydrator $postHydrator,
        private BlogUserHydrator $blogUserHydrator,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Blog
    {
        $id = $uriVariables['blog_id'] ?? $uriVariables['id'] ?? null;
        if (null === $id) {
            return null;
        }

        /** @var \App\Blog\Domain\Model\Blog $blog */
        $blog = $this->blogRepository->find((int) $id);
        if (null === $blog) {
            return null;
        }

        $blogResource = $this->blogHydrator->hydrate($blog);
        $blogResource->posts = array_map(fn ($post) => $this->postHydrator->hydrate($post), $blog->getPosts());
        $blogResource->blogUsers = array_map(
            function ($blogUser) {
                $blogUserResource = $this->blogUserHydrator->hydrate($blogUser);
                $blogUserResource->blog = $this->blogHydrator->hydrate($blogUser->getBlog());

                return $blogUserResource;
            },
            $blog->getBlogUsers()
        );

        return $blogResource;
    }
}
