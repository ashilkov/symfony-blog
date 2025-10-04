<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Factory;

use App\Blog\Domain\Model\Post;
use App\Blog\Domain\Value\Blog\BlogId;
use App\Blog\Domain\Value\Common\Content;
use App\Blog\Domain\Value\Common\UserId;
use App\Blog\Domain\Value\Post\PostTitle;

class PostFactory
{
    public function create(PostTitle $title, Content $content, UserId $userId, BlogId $blogId): Post
    {
        return new Post(title: $title, content: $content, userId: $userId, blogId: $blogId);
    }
}
