<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Doctrine\Assembler;

use App\Blog\Domain\Model\Blog as BlogDomain;
use App\Blog\Domain\Model\BlogUser as BlogUserDomain;
use App\Blog\Domain\Model\Comment as CommentDomain;
use App\Blog\Domain\Model\EntityInterface;
use App\Blog\Domain\Model\Post as PostDomain;
use App\Blog\Domain\Model\Subscription as SubscriptionDomain;
use App\Blog\Infrastructure\Doctrine\Entity\Blog;
use App\Blog\Infrastructure\Doctrine\Entity\BlogUser;
use App\Blog\Infrastructure\Doctrine\Entity\Comment;
use App\Blog\Infrastructure\Doctrine\Entity\DoctrineEntityInterface;
use App\Blog\Infrastructure\Doctrine\Entity\Post;
use App\Blog\Infrastructure\Doctrine\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;

readonly class EntityAssembler implements AssemblerInterface
{
    public function __construct(
        private BlogAssembler $blogAssembler,
        private PostAssembler $postAssembler,
        private BlogUserAssembler $blogUserAssembler,
        private SubscriptionAssembler $subscriptionAssembler,
        private CommentAssembler $commentAssembler,
        private EntityManagerInterface $em,
    ) {
    }

    public function toDomain(DoctrineEntityInterface $entity): EntityInterface
    {
        $className = get_class($entity);
        switch ($className) {
            case Blog::class:
                /** @var Blog $entity */
                $domain = $this->blogAssembler->toDomain($entity);
                foreach ($entity->posts as $post) {
                    $domain->addPost($this->postAssembler->toDomain($post));
                }
                foreach ($entity->blogUsers as $blogUser) {
                    $domain->addBlogUser($this->blogUserAssembler->toDomain($blogUser));
                }
                foreach ($entity->subscriptions as $subscription) {
                    $domain->addSubscription($this->subscriptionAssembler->toDomain($subscription));
                }
                break;
            case Post::class:
                /** @var Post $entity */
                $domain = $this->postAssembler->toDomain($entity);
                if (!empty($entity->blog)) {
                    $domain->setBlog($this->blogAssembler->toDomain($entity->blog));
                }
                foreach ($entity->comments as $comment) {
                    $domain->addComment($this->commentAssembler->toDomain($comment));
                }
                break;
            case BlogUser::class:
                /** @var BlogUser $entity */
                $domain = $this->blogUserAssembler->toDomain($entity);
                if (!empty($entity->blog)) {
                    $domain->setBlog($this->blogAssembler->toDomain($entity->blog));
                }
                break;

            case Comment::class:
                /** @var Comment $entity */
                $domain = $this->commentAssembler->toDomain($entity);
                if (!empty($entity->post)) {
                    $domain->setPost($this->postAssembler->toDomain($entity->post));
                }
                break;
            case Subscription::class:
                /** @var Subscription $entity */
                $domain = $this->subscriptionAssembler->toDomain($entity);
                if (!empty($entity->blog)) {
                    $domain->setBlog($this->blogAssembler->toDomain($entity->blog));
                }
                break;
            default:
                throw new \Exception('Unsupported entity type: '.$className);
        }

        return $domain;
    }

    public function toDoctrineEntity(EntityInterface $entity, ?DoctrineEntityInterface $existingEntity = null): DoctrineEntityInterface
    {
        $className = get_class($entity);
        switch ($className) {
            case BlogDomain::class:
                /** @var BlogDomain $entity */
                $doctrineEntity = $this->blogAssembler->toDoctrineEntity($entity, $existingEntity);
                $doctrineEntity->posts->clear();
                foreach ($entity->getPosts() as $post) {
                    $doctrineEntity->posts->add($this->em->getReference(Post::class, $post->getId()));
                }
                $doctrineEntity->blogUsers->clear();

                foreach ($entity->getBlogUsers() as $blogUser) {
                    if ($doctrineEntity->id !== null) {
                        $doctrineEntity->blogUsers->add(
                            $this->em->getReference(
                                BlogUser::class,
                                ['blogId' => $blogUser->getBlog()->getId(), 'userId' => $blogUser->getUserId()]
                            )
                        );
                    }
                }
                $doctrineEntity->subscriptions->clear();
                foreach ($entity->getSubscriptions() as $subscription) {
                    $doctrineEntity->subscriptions->add(
                        $this->em->getReference(Subscription::class, $subscription->getId())
                    );
                }

                break;
            case PostDomain::class:
                /** @var PostDomain $entity */
                $doctrineEntity = $this->postAssembler->toDoctrineEntity($entity, $existingEntity);
                if (!empty($entity->getBlog()) && null !== $entity->getBlog()->getId()) {
                    $doctrineEntity->blog = $this->em->getReference(
                        Blog::class,
                        $entity->getBlog()->getId()
                    );
                }

                $doctrineEntity->comments->clear();
                foreach ($entity->getComments() as $comment) {
                    $doctrineEntity->comments->add($this->em->getReference(Comment::class, $comment->getId()));
                }
                break;
            case BlogUserDomain::class:
                /** @var BlogUserDomain $entity */
                $doctrineEntity = $this->blogUserAssembler->toDoctrineEntity($entity, $existingEntity);
                if (!empty($entity->getBlog()) && null !== $entity->getBlog()->getId()) {
                    $doctrineEntity->blog = $this->em->getReference(Blog::class, $entity->getBlog()->getId());
                }
                break;
            case CommentDomain::class:
                $doctrineEntity = $this->commentAssembler->toDoctrineEntity($entity, $existingEntity);
                if (!empty($entity->getPost()) && null !== $entity->getPost()->getId()) {
                    $doctrineEntity->post = $this->em->getReference(Post::class, $entity->getPost()->getId());
                }
                break;

            case SubscriptionDomain::class:
                /** @var SubscriptionDomain $entity */
                $doctrineEntity = $this->subscriptionAssembler->toDoctrineEntity($entity, $existingEntity);
                if (!empty($entity->getBlog()) && null !== $entity->getBlog()->getId()) {
                    $doctrineEntity->blog = $this->em->getReference(Blog::class, $entity->getBlog()->getId());
                }
                break;
            default:
                throw new \Exception('Unsupported entity type: '.$className);
        }

        return $doctrineEntity;
    }
}
