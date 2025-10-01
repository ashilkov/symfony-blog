<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Doctrine\Assembler;

use App\Blog\Domain\Model\EntityInterface;
use App\Blog\Infrastructure\Doctrine\Entity\DoctrineEntityInterface;

interface AssemblerInterface
{
    public function toDomain(DoctrineEntityInterface $entity): EntityInterface;

    public function toDoctrineEntity(EntityInterface $entity, ?DoctrineEntityInterface $existingEntity = null): DoctrineEntityInterface;
}
