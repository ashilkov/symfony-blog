<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Repository;

use App\Blog\Domain\Repository\CommentRepositoryInterface;
use App\Blog\Infrastructure\Doctrine\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends AbstractRepository implements CommentRepositoryInterface
{
    public function getEntityType(): string
    {
        return Comment::class;
    }
}
