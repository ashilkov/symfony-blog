<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Doctrine\Assembler;

use App\Blog\Domain\Model\EntityInterface;
use App\Blog\Domain\Model\Post as Domain;
use App\Blog\Infrastructure\Doctrine\Entity\DoctrineEntityInterface;
use App\Blog\Infrastructure\Doctrine\Entity\Post as DoctrineEntity;

class PostAssembler
{
    public function toDomain(DoctrineEntityInterface|DoctrineEntity $entity): Domain
    {
        if (!$entity instanceof DoctrineEntity) {
            throw new \InvalidArgumentException('Entity must be an instance of Blog');
        }

        return new Domain(
            id: $entity->id,
            title: $entity->title,
            content: $entity->content,
            userId: $entity->userId,
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
        $record->title = $entity->getTitle();
        $record->content = $entity->getContent();
        $record->userId = $entity->getUserId();
        $record->createdAt = $entity->getCreatedAt();
        $record->updatedAt = $entity->getUpdatedAt();

        return $record;
    }
}
