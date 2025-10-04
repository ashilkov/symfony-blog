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
use App\Blog\Domain\Value\Blog\BlogDescription;
use App\Blog\Domain\Value\Blog\BlogId;
use App\Blog\Domain\Value\Blog\BlogName;
use App\Blog\Infrastructure\Doctrine\Entity\Blog as DoctrineEntity;
use App\Blog\Infrastructure\Doctrine\Entity\BlogUser;
use App\Blog\Infrastructure\Doctrine\Entity\DoctrineEntityInterface;
use App\Blog\Infrastructure\Doctrine\Entity\Post;
use App\Blog\Infrastructure\Doctrine\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;

readonly class BlogAssembler implements AssemblerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PostAssembler $postAssembler,
        private BlogUserAssembler $blogUserAssembler,
        private SubscriptionAssembler $subscriptionAssembler,
    ) {
    }

    public function toDomain(DoctrineEntityInterface|DoctrineEntity $entity): Domain
    {
        if (!$entity instanceof DoctrineEntity) {
            throw new \InvalidArgumentException('Entity must be an instance of Blog');
        }

        $domainEntity = new Domain(
            id: new BlogId($entity->id),
            name: new BlogName($entity->name),
            description: new BlogDescription($entity->description),
            createdAt: $entity->createdAt,
            updatedAt: $entity->updatedAt
        );

        foreach ($entity->posts as $post) {
            $domainEntity->addPost($this->postAssembler->toDomain($post));
        }

        foreach ($entity->blogUsers as $blogUser) {
            $domainEntity->addBlogUser($this->blogUserAssembler->toDomain($blogUser));
        }
        foreach ($entity->subscriptions as $subscription) {
            $domainEntity->addSubscription($this->subscriptionAssembler->toDomain($subscription));
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
        $doctrineEntity->name = $entity->getName()->value();
        $doctrineEntity->description = $entity->getDescription()->value();
        $doctrineEntity->createdAt = $entity->getCreatedAt();
        $doctrineEntity->updatedAt = $entity->getUpdatedAt();

        /* posts */
        $doctrineEntity->posts->clear();
        foreach ($entity->getPosts() as $post) {
            $doctrineEntity->posts->add($this->entityManager->getReference(Post::class, $post->getId()->value()));
        }

        /* blog users */
        $doctrineEntity->blogUsers->clear();
        /** @var \App\Blog\Domain\Model\BlogUser $blogUser */
        foreach ($entity->getBlogUsers() as $blogUser) {
            if (null !== $doctrineEntity->id) {
                $doctrineEntity->blogUsers->add(
                    $this->entityManager->getReference(
                        BlogUser::class,
                        ['blogId' => $blogUser->getBlogId()->value(), 'userId' => $blogUser->getUserId()->value()]
                    )
                );
            }
        }

        /* subscriptions */
        $doctrineEntity->subscriptions->clear();
        foreach ($entity->getSubscriptions() as $subscription) {
            $doctrineEntity->subscriptions->add(
                $this->entityManager->getReference(Subscription::class, $subscription->getId()->value())
            );
        }

        return $doctrineEntity;
    }
}
