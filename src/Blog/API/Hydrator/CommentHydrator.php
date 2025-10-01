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

class CommentHydrator
{
    public function hydrate(Comment $comment): CommentResource
    {
        return new CommentResource(
            id: $comment->getId(),
            userId: $comment->getUserId(),
            postId: $comment->getPost()->getId(),
            content: $comment->getContent(),
            createdAt: $comment->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $comment->getUpdatedAt()->format('Y-m-d H:i:s'),
        );
    }
}
