<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\State\Blog;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\ArrayPaginator;
use ApiPlatform\State\ProviderInterface;
use App\Blog\API\Hydrator\BlogHydrator;
use App\Blog\API\Hydrator\BlogUserHydrator;
use App\Blog\API\Hydrator\PostHydrator;
use App\Blog\Application\CurrentUserProviderInterface;
use App\Blog\Domain\Model\Blog;
use App\Blog\Domain\Model\Subscription;
use Doctrine\ORM\EntityManagerInterface;

readonly class SubscribedCollectionProvider implements ProviderInterface
{
    public function __construct(
        private BlogHydrator $blogHydrator,
        private PostHydrator $postHydrator,
        private BlogUserHydrator $blogUserHydrator,
        private EntityManagerInterface $em,
        private CurrentUserProviderInterface $userProvider,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Resolve pagination from context (defaults if not provided)
        $page = (int) ($context['filters']['page'] ?? 1);
        $itemsPerPage = (int) ($context['filters']['itemsPerPage'] ?? 30);
        $page = $page > 0 ? $page : 1;
        $itemsPerPage = $itemsPerPage > 0 ? $itemsPerPage : 30;
        $offset = ($page - 1) * $itemsPerPage;

        $userId = $this->userProvider->getUserId();
        if (null === $userId) {
            return new ArrayPaginator([], 0, 0);
        }

        // Query Blogs joined with Subscriptions for the current user
        $qb = $this->em->createQueryBuilder()
            ->select('b')
            ->from(Blog::class, 'b')
            ->innerJoin(Subscription::class, 's', 'WITH', 's.blog = b AND s.subscriberId = :uid')
            ->setParameter('uid', $userId)
            ->setFirstResult($offset)
            ->setMaxResults($itemsPerPage);

        /** @var Blog[] $blogs */
        $blogs = $qb->getQuery()->getResult();

        // Total count for pagination
        $countQb = $this->em->createQueryBuilder()
            ->select('COUNT(b.id)')
            ->from(Blog::class, 'b')
            ->innerJoin(Subscription::class, 's', 'WITH', 's.blog = b AND s.subscriberId = :uid')
            ->setParameter('uid', $userId);

        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        $items = array_map(function ($blog) {
            $blogResource = $this->blogHydrator->hydrate($blog);
            $blogResource->posts = array_map(
                fn ($post) => $this->postHydrator->hydrate($post),
                $blog->getPosts()->toArray()
            );
            $blogResource->blogUsers = array_map(
                fn ($blogUser) => $this->blogUserHydrator->hydrate($blogUser),
                $blog->getBlogUsers()->toArray()
            );

            return $blogResource;
        }, $blogs);

        // Return a paginator to satisfy API Platform expectations
        return new ArrayPaginator($items, 0, $total);
    }
}
