<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Repository;

use App\Blog\Domain\Model\EntityInterface;
use App\Blog\Infrastructure\Doctrine\Assembler\AssemblerInterface;
use App\Blog\Infrastructure\Doctrine\Entity\DoctrineEntityInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractRepository extends ServiceEntityRepository
{
    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
        protected readonly AssemblerInterface $entityAssembler,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, $this->getEntityType());
    }

    public function find(mixed $id, int|LockMode|null $lockMode = null, ?int $lockVersion = null): ?object
    {
        $entity = parent::find($id, $lockMode, $lockVersion);

        return $entity ? $this->entityAssembler->toDomain($entity) : null;
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object
    {
        /** @var DoctrineEntityInterface $entity */
        $entity = parent::findOneBy($criteria, $orderBy);

        return $entity ? $this->entityAssembler->toDomain($entity) : null;
    }

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        $entities = [];
        $doctrineEntities = parent::findBy($criteria, $orderBy, $limit, $offset);
        foreach ($doctrineEntities as $doctrineEntity) {
            $entities[] = $this->entityAssembler->toDomain($doctrineEntity);
        }

        return $entities;
    }

    public function save(EntityInterface $entity, bool $flush = false): void
    {
        $existing = null;
        if ($entity->getId()) {
            $existing = parent::find($entity->getId()->value());
        }
        $doctrineEntity = $this->entityAssembler->toDoctrineEntity($entity, $existing);

        $this->saveBefore($entity, $doctrineEntity);

        $this->entityManager->persist($doctrineEntity);
        if ($flush) {
            $this->entityManager->flush();
        }

        $this->saveAfter($entity, $doctrineEntity);
    }

    public function remove(EntityInterface $entity, bool $flush = false): void
    {
        $existing = null;
        if ($entity->getId()) {
            $existing = parent::find($entity->getId()->value());
        }
        $doctrineEntity = $this->entityAssembler->toDoctrineEntity($entity, $existing);
        $this->entityManager->remove($doctrineEntity);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    abstract public function getEntityType(): string;

    protected function saveBefore(EntityInterface $entity, object $doctrineEntity): void
    {
    }

    protected function saveAfter(EntityInterface $entity, object $doctrineEntity): void
    {
        if (method_exists($entity, 'setCreatedAt')
            && property_exists($doctrineEntity, 'createdAt')
            && null !== $doctrineEntity->createdAt
        ) {
            $entity->setCreatedAt($doctrineEntity->createdAt);
        }

        if (method_exists($entity, 'setUpdatedAt')
            && property_exists($doctrineEntity, 'updatedAt')
            && null !== $doctrineEntity->updatedAt
        ) {
            $entity->setUpdatedAt($doctrineEntity->updatedAt);
        }
    }
}
