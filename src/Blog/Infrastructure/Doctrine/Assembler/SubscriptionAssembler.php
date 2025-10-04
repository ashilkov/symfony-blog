<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Doctrine\Assembler;

use App\Blog\Domain\Model\EntityInterface;
use App\Blog\Domain\Model\Subscription as Domain;
use App\Blog\Domain\Value\Blog\BlogId;
use App\Blog\Domain\Value\Common\UserId;
use App\Blog\Domain\Value\Subscription\SubscriptionId;
use App\Blog\Infrastructure\Doctrine\Entity\Blog;
use App\Blog\Infrastructure\Doctrine\Entity\DoctrineEntityInterface;
use App\Blog\Infrastructure\Doctrine\Entity\Subscription as DoctrineEntity;
use Doctrine\ORM\EntityManagerInterface;

class SubscriptionAssembler implements AssemblerInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function toDomain(DoctrineEntityInterface|DoctrineEntity $entity): Domain
    {
        return new Domain(
            id: new SubscriptionId($entity->id),
            blogId: new BlogId($entity->blog->id),
            subscriberId: new UserId($entity->subscriberId),
            createdAt: $entity->createdAt,
            updatedAt: $entity->updatedAt
        );
    }

    public function toDoctrineEntity(EntityInterface $entity, ?DoctrineEntityInterface $existingEntity = null): DoctrineEntity
    {
        if (!$entity instanceof Domain) {
            throw new \InvalidArgumentException('Entity must be an instance of Blog');
        }
        $doctrineEntity = $existingEntity ?? new DoctrineEntity();

        $doctrineEntity->id = $entity->getId()?->value();
        $doctrineEntity->subscriberId = $entity->getSubscriberId()->value();
        $doctrineEntity->blog = $this->entityManager->getReference(Blog::class, $entity->getBlogId()->value());
        $doctrineEntity->createdAt = $entity->getCreatedAt();
        $doctrineEntity->updatedAt = $entity->getUpdatedAt();

        return $doctrineEntity;
    }
}
