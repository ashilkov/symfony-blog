<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Repository;

use App\Blog\Domain\Repository\BlogUserRepositoryInterface;
use App\Blog\Infrastructure\Doctrine\Entity\BlogUser;

class BlogUserRepository extends AbstractRepository implements BlogUserRepositoryInterface
{
    public function getEntityType(): string
    {
        return BlogUser::class;
    }
}
