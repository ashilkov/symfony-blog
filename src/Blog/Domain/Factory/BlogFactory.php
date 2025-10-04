<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Factory;

use App\Blog\Domain\Model\Blog;
use App\Blog\Domain\Value\Blog\BlogDescription;
use App\Blog\Domain\Value\Blog\BlogName;

class BlogFactory
{
    public function create(BlogName $name, BlogDescription $description): Blog
    {
        return new Blog(name: $name, description: $description);
    }
}
