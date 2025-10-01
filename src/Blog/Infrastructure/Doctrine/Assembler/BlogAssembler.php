<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Doctrine\Assembler;

use App\Blog\Domain\Model\Blog as Domain;
use App\Blog\Domain\Model\EntityInterface;
use App\Blog\Infrastructure\Doctrine\Entity\Blog as DoctrineEntity;
use App\Blog\Infrastructure\Doctrine\Entity\DoctrineEntityInterface;

class BlogAssembler implements AssemblerInterface
{
    public function toDomain(DoctrineEntityInterface|DoctrineEntity $entity): Domain
    {
        if (!$entity instanceof DoctrineEntity) {
            throw new \InvalidArgumentException('Entity must be an instance of Blog');
        }

        return new Domain(
            id: $entity->id,
            name: $entity->name,
            description: $entity->description,
            createdAt: $entity->createdAt,
            updatedAt: $entity->updatedAt
        );
    }

    public function toDoctrineEntity(EntityInterface|Domain $entity, ?DoctrineEntityInterface $existingEntity = null): DoctrineEntity
    {
        if (!$entity instanceof Domain) {
            throw new \InvalidArgumentException('Entity must be an instance of Blog');
        }
        $record = $existingEntity ?? new DoctrineEntity();

        $record->id = $entity->getId();
        $record->name = $entity->getName();
        $record->description = $entity->getDescription();
        $record->createdAt = $entity->getCreatedAt();
        $record->updatedAt = $entity->getUpdatedAt();

        return $record;
    }
}
