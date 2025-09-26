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

class BlogHydrator
{
    public function hydrate(Blog $blog): BlogResource
    {
        return new BlogResource(
            id: $blog->getId(),
            name: $blog->getName(),
            description: $blog->getDescription(),
        );
    }
}
