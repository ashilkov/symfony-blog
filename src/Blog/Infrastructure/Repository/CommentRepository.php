<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Repository;

use App\Blog\Domain\Model\EntityInterface;
use App\Blog\Domain\Repository\CommentRepositoryInterface;
use App\Blog\Domain\Value\Comment\CommentId;
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

    protected function saveAfter(EntityInterface $entity, object $doctrineEntity): void
    {
        parent::saveAfter($entity, $doctrineEntity);

        if (method_exists($entity, 'assignId') && property_exists($doctrineEntity, 'id')) {
            $entity->assignId(new CommentId($doctrineEntity->id));
        }
    }
}
