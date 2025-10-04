<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\Hydrator;

use App\Blog\API\Resource\Comment as CommentResource;
use App\Blog\Domain\Model\Comment;
use App\Blog\Domain\User\UserReadModelPortInterface;

class CommentHydrator
{
    public function __construct(private UserReadModelPortInterface $userReadModelPort)
    {
    }

    public function hydrate(Comment $comment): CommentResource
    {
        return new CommentResource(
            id: $comment->getId()->value(),
            author: $this->userReadModelPort->findSummaryById($comment->getUserId()->value())->username,
            postId: $comment->getPostId()->value(),
            content: $comment->getContent()->value(),
            createdAt: $comment->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $comment->getUpdatedAt()->format('Y-m-d H:i:s'),
        );
    }
}
