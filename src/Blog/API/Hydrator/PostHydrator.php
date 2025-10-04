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
use App\Blog\Domain\User\UserReadModelPortInterface;

readonly class PostHydrator
{
    public function __construct(
        private UserReadModelPortInterface $userReadModelPort,
        private CommentHydrator $commentHydrator,
    ) {
    }

    public function hydrate(Post $post): PostResource
    {
        $postResource = new PostResource(
            id: $post->getId()->value(),
            title: $post->getTitle()->value(),
            content: $post->getContent()->value(),
            createdAt: $post->getCreatedAt()?->format('Y-m-d H:i:s'),
            updatedAt: $post->getUpdatedAt()?->format('Y-m-d H:i:s'),
            blogId: $post->getBlogId()->value(),
            author: $this->userReadModelPort->findSummaryById($post->getAuthorId()->value())->username,
        );

        $postResource->comments = array_map(fn ($comment) => $this->commentHydrator->hydrate($comment), $post->getComments());

        return $postResource;
    }
}
