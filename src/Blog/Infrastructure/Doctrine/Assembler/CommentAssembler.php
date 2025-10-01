<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Doctrine\Assembler;

use App\Blog\Domain\Model\Comment as Domain;
use App\Blog\Domain\Model\EntityInterface;
use App\Blog\Infrastructure\Doctrine\Entity\Comment as DoctrineEntity;
use App\Blog\Infrastructure\Doctrine\Entity\DoctrineEntityInterface;

class CommentAssembler implements AssemblerInterface
{
    public function toDomain(DoctrineEntityInterface|DoctrineEntity $entity): Domain
    {
        return new Domain(
            id: $entity->id,
            userId: $entity->userId,
            content: $entity->content,
            createdAt: $entity->createdAt,
            updatedAt: $entity->updatedAt,
        );
    }

    public function toDoctrineEntity(EntityInterface $entity, ?DoctrineEntityInterface $existingEntity = null): DoctrineEntity
    {
        if (!$entity instanceof Domain) {
            throw new \InvalidArgumentException('Entity must be an instance of Blog');
        }
        $record = $existingEntity ?? new DoctrineEntity();

        $record->id = $entity->getId();
        $record->userId = $entity->getUserId();
        $record->content = $entity->getContent();
        $record->createdAt = $entity->getCreatedAt();
        $record->updatedAt = $entity->getUpdatedAt();

        return $record;
    }
}
