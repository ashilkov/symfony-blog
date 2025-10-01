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
use App\Blog\Infrastructure\Doctrine\Entity\BlogUser as DoctrineEntity;
use App\Blog\Infrastructure\Doctrine\Entity\DoctrineEntityInterface;

class BlogUserAssembler implements AssemblerInterface
{
    public function toDomain(DoctrineEntityInterface|DoctrineEntity $entity): Domain
    {
        return new Domain(
            userId: $entity->userId,
            role: $entity->role,
        );
    }

    public function toDoctrineEntity(EntityInterface $entity, ?DoctrineEntityInterface $existingEntity = null): DoctrineEntity
    {
        if (!$entity instanceof Domain) {
            throw new \InvalidArgumentException('Entity must be an instance of Blog');
        }
        $record = $existingEntity ?? new DoctrineEntity();
        $record->userId = $entity->getUserId();
        $record->role = $entity->getRole();

        return $record;
    }
}
