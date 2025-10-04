<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Doctrine\Assembler;

use App\Blog\Domain\Model\BlogUser as Domain;
use App\Blog\Domain\Model\EntityInterface;
use App\Blog\Domain\Value\Blog\BlogId;
use App\Blog\Domain\Value\Common\UserId;
use App\Blog\Infrastructure\Doctrine\Entity\Blog;
use App\Blog\Infrastructure\Doctrine\Entity\BlogUser as DoctrineEntity;
use App\Blog\Infrastructure\Doctrine\Entity\DoctrineEntityInterface;
use Doctrine\ORM\EntityManagerInterface;

class BlogUserAssembler implements AssemblerInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function toDomain(DoctrineEntityInterface|DoctrineEntity $entity): Domain
    {
        return new Domain(
            userId: new UserId($entity->userId),
            role: $entity->role,
            blogId: new BlogId($entity->blog->id),
        );
    }

    public function toDoctrineEntity(EntityInterface $entity, ?DoctrineEntityInterface $existingEntity = null): DoctrineEntity
    {
        if (!$entity instanceof Domain) {
            throw new \InvalidArgumentException('Entity must be an instance of Blog');
        }
        $doctrineEntity = $existingEntity ?? new DoctrineEntity();
        $doctrineEntity->userId = $entity->getUserId()->value();
        $doctrineEntity->role = $entity->getRole();
        $doctrineEntity->blog = $this->entityManager->getReference(Blog::class, $entity->getBlogId()->value());

        return $doctrineEntity;
    }
}
