<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\Hydrator;

use App\Blog\API\Resource\BlogUser as BlogUserResource;
use App\Blog\Domain\Model\BlogUser;

class BlogUserHydrator
{
    public function hydrate(BlogUser $blogUser): BlogUserResource
    {
        return new BlogUserResource(
            role: $blogUser->getRole(),
            userId: $blogUser->getUserId(),
        );
    }
}
