<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\Hydrator;

use App\Blog\API\Resource\Blog as BlogResource;
use App\Blog\Domain\Model\Blog;

readonly class BlogHydrator
{
    public function __construct(
        private BlogUserHydrator $blogUserHydrator,
        private PostHydrator $postHydrator,
    ) {
    }

    public function hydrate(Blog $blog): BlogResource
    {
        $blogResource = new BlogResource(
            id: $blog->getId()->value(),
            name: $blog->getName()->value(),
            description: $blog->getDescription()->value(),
            createdAt: $blog->getCreatedAt()?->format('Y-m-d H:i:s'),
            updatedAt: $blog->getUpdatedAt()?->format('Y-m-d H:i:s'),
        );

        $blogResource->posts = array_map(fn ($post) => $this->postHydrator->hydrate($post), $blog->getPosts());
        $blogResource->blogUsers = array_map(fn ($blogUser) => $this->blogUserHydrator->hydrate($blogUser), $blog->getBlogUsers());

        return $blogResource;
    }
}
