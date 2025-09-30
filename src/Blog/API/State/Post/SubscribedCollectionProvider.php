<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\State\Post;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\ArrayPaginator;
use ApiPlatform\State\ProviderInterface;
use App\Blog\API\Hydrator\BlogHydrator;
use App\Blog\API\Hydrator\PostHydrator;
use App\Blog\Application\CurrentUserProviderInterface;
use App\Blog\Domain\Model\Post;
use App\Blog\Domain\Model\Subscription as DomainSubscription;
use Doctrine\ORM\EntityManagerInterface;

readonly class SubscribedCollectionProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private PostHydrator $postHydrator,
        private BlogHydrator $blogHydrator,
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

        // Query Posts from blogs the user is subscribed to
        $qb = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Post::class, 'p')
            ->innerJoin('p.blog', 'b')
            ->innerJoin(DomainSubscription::class, 's', 'WITH', 's.blog = b AND s.subscriberId = :uid')
            ->setParameter('uid', $userId)
            ->setFirstResult($offset)
            ->setMaxResults($itemsPerPage);

        /** @var Post[] $posts */
        $posts = $qb->getQuery()->getResult();

        // Total count for pagination
        $countQb = $this->em->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(Post::class, 'p')
            ->innerJoin('p.blog', 'b')
            ->innerJoin(DomainSubscription::class, 's', 'WITH', 's.blog = b AND s.subscriberId = :uid')
            ->setParameter('uid', $userId);

        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        $items = array_map(
            function (Post $post) {
                $postResource = $this->postHydrator->hydrate($post);
                $postResource->blog = $this->blogHydrator->hydrate($post->getBlog());

                return $postResource;
            },
            $posts
        );

        return new ArrayPaginator($items, 0, $total);
    }
}
