<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Repository;

use App\Blog\Domain\Model\EntityInterface;
use App\Blog\Domain\Repository\PostRepositoryInterface;
use App\Blog\Domain\Value\Post\PostId;
use App\Blog\Infrastructure\Doctrine\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends AbstractRepository implements PostRepositoryInterface
{
    public function getEntityType(): string
    {
        return Post::class;
    }

    protected function saveAfter(EntityInterface $entity, object $doctrineEntity): void
    {
        parent::saveAfter($entity, $doctrineEntity);

        if (method_exists($entity, 'assignId') && property_exists($doctrineEntity, 'id')) {
            $entity->assignId(new PostId($doctrineEntity->id));
        }
    }
}
