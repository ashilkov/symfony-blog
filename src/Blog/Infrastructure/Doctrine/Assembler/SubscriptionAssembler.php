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
use App\Blog\Infrastructure\Doctrine\Entity\DoctrineEntityInterface;
use App\Blog\Infrastructure\Doctrine\Entity\Subscription as DoctrineEntity;

class SubscriptionAssembler implements AssemblerInterface
{
    public function toDomain(DoctrineEntityInterface|DoctrineEntity $entity): Domain
    {
        return new Domain(
            id: $entity->id,
            subscriberId: $entity->subscriberId,
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

        $doctrineEntity->id = $entity->getId();
        $doctrineEntity->subscriberId = $entity->getSubscriberId();
        $doctrineEntity->createdAt = $entity->getCreatedAt();
        $doctrineEntity->updatedAt = $entity->getUpdatedAt();

        return $doctrineEntity;
    }
}
