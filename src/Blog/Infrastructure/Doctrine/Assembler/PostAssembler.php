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
use App\Blog\Domain\Value\Blog\BlogId;
use App\Blog\Domain\Value\Common\Content;
use App\Blog\Domain\Value\Common\UserId;
use App\Blog\Domain\Value\Post\PostId;
use App\Blog\Domain\Value\Post\PostTitle;
use App\Blog\Infrastructure\Doctrine\Entity\Blog;
use App\Blog\Infrastructure\Doctrine\Entity\Comment;
use App\Blog\Infrastructure\Doctrine\Entity\DoctrineEntityInterface;
use App\Blog\Infrastructure\Doctrine\Entity\Post as DoctrineEntity;
use Doctrine\ORM\EntityManagerInterface;

readonly class PostAssembler implements AssemblerInterface
{
    public function __construct(
        private CommentAssembler $commentAssembler,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function toDomain(DoctrineEntityInterface|DoctrineEntity $entity): Domain
    {
        if (!$entity instanceof DoctrineEntity) {
            throw new \InvalidArgumentException('Entity must be an instance of Blog');
        }

        $domainEntity = new Domain(
            id: new PostId($entity->id),
            title: new PostTitle($entity->title),
            content: new Content($entity->content),
            userId: new UserId($entity->userId),
            blogId: new BlogId($entity->blog->id),
            createdAt: $entity->createdAt,
            updatedAt: $entity->updatedAt
        );

        foreach ($entity->comments as $comment) {
            $domainEntity->addComment($this->commentAssembler->toDomain($comment));
        }

        return $domainEntity;
    }

    public function toDoctrineEntity(EntityInterface|Domain $entity, ?DoctrineEntityInterface $existingEntity = null): DoctrineEntity
    {
        if (!$entity instanceof Domain) {
            throw new \InvalidArgumentException('Entity must be an instance of Blog');
        }
        $doctrineEntity = $existingEntity ?? new DoctrineEntity();

        $doctrineEntity->id = $entity->getId()?->value();
        $doctrineEntity->title = $entity->getTitle()->value();
        $doctrineEntity->content = $entity->getContent()->value();
        $doctrineEntity->userId = $entity->getAuthorId()->value();
        $doctrineEntity->blog = $this->entityManager->getReference(Blog::class, $entity->getBlogId()->value());
        $doctrineEntity->createdAt = $entity->getCreatedAt();
        $doctrineEntity->updatedAt = $entity->getUpdatedAt();

        $doctrineEntity->comments->clear();
        foreach ($entity->getComments() as $comment) {
            $doctrineEntity->comments->add($this->entityManager->getReference(Comment::class, $comment->getId()->value()));
        }

        return $doctrineEntity;
    }
}
