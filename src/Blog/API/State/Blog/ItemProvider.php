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
use App\Blog\API\Resource\Blog;
use App\Blog\Domain\Repository\BlogRepositoryInterface;

readonly class ItemProvider implements ProviderInterface
{
    public function __construct(
        private BlogRepositoryInterface $blogRepository,
        private BlogHydrator $blogHydrator,
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

        return $this->blogHydrator->hydrate($blog);
    }
}
