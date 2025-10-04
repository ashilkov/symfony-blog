<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Factory;

use App\Blog\Domain\Model\Comment;
use App\Blog\Domain\Value\Common\Content;
use App\Blog\Domain\Value\Common\UserId;
use App\Blog\Domain\Value\Post\PostId;

class CommentFactory
{
    public function create(Content $content, UserId $userId, PostId $postId): Comment
    {
        return new Comment(userId: $userId, content: $content, postId: $postId);
    }
}
