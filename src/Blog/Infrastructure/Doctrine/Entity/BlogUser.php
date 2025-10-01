<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Doctrine\Entity;

use App\Blog\Domain\Enum\BlogUserRole;

class BlogUser implements DoctrineEntityInterface
{
    public ?Blog $blog = null;

    public ?int $userId = null;

    public ?BlogUserRole $role = null;
}
