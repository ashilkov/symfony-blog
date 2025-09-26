<?php
/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\Hydrator;

use App\Blog\API\Resource\Post as PostResource;
use App\Blog\Domain\Model\Post;

class PostHydrator
{
    public function hydrate(Post $post): PostResource
    {
        return new PostResource(
            id: $post->getId(),
            title: $post->getTitle(),
            content: $post->getContent(),
            createdAt: $post->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $post->getUpdatedAt()->format('Y-m-d H:i:s'),
        );
    }
}
