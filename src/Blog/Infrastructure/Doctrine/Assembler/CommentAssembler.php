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
use App\Blog\Domain\Value\Comment\CommentId;
use App\Blog\Domain\Value\Common\Content;
use App\Blog\Domain\Value\Common\UserId;
use App\Blog\Domain\Value\Post\PostId;
use App\Blog\Infrastructure\Doctrine\Entity\Comment as DoctrineEntity;
use App\Blog\Infrastructure\Doctrine\Entity\DoctrineEntityInterface;
use App\Blog\Infrastructure\Doctrine\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;

readonly class CommentAssembler implements AssemblerInterface
{

    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function toDomain(DoctrineEntityInterface|DoctrineEntity $entity): Domain
    {
        return new Domain(
            id: new CommentId($entity->id),
            userId: new UserId($entity->userId),
            content: new Content($entity->content),
            postId: new PostId($entity->post->id),
            createdAt: $entity->createdAt,
            updatedAt: $entity->updatedAt,
        );
    }

    public function toDoctrineEntity(EntityInterface $entity, ?DoctrineEntityInterface $existingEntity = null): DoctrineEntity
    {
        if (!$entity instanceof Domain) {
            throw new \InvalidArgumentException('Entity must be an instance of Blog');
        }
        $doctrineEntity = $existingEntity ?? new DoctrineEntity();

        $doctrineEntity->id = $entity->getId()?->value();
        $doctrineEntity->userId = $entity->getUserId()->value();
        $doctrineEntity->content = $entity->getContent()->value();
        $doctrineEntity->post = $this->entityManager->getReference(Post::class, $entity->getPostId()->value());
        $doctrineEntity->createdAt = $entity->getCreatedAt();
        $doctrineEntity->updatedAt = $entity->getUpdatedAt();

        return $doctrineEntity;
    }
}
