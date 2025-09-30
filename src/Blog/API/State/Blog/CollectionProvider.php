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

readonly class CollectionProvider implements ProviderInterface
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
        $qb = $this->em->createQueryBuilder()
            ->select('b AS blog')
            ->addSelect('(CASE WHEN s.id IS NULL THEN 0 ELSE 1 END) AS subscribedFlag')
            ->addSelect('(SELECT COUNT(b2.id) FROM '.Blog::class.' b2) AS totalCount')
            ->from(Blog::class, 'b')
            ->leftJoin(
                Subscription::class,
                's',
                'WITH',
                's.blog = b AND s.subscriberId = :uid'
            )
            ->setParameter('uid', $userId) // NULL when not logged in, producing no match in the ON clause
            ->setFirstResult($offset)
            ->setMaxResults($itemsPerPage);

        // Row format: ['blog' => BlogEntity, 'subscribedFlag' => 0|1, 'totalCount' => N]
        $rows = $qb->getQuery()->getResult();

        $total = 0;
        if (!empty($rows)) {
            $total = (int) $rows[0]['totalCount'];
        }

        $items = array_map(function (array $row) {
            /** @var Blog $blog */
            $blog = $row['blog'];
            $subscribed = 1 === (int) $row['subscribedFlag'];

            $blogResource = $this->blogHydrator->hydrate($blog);
            $blogResource->posts = array_map(
                fn ($post) => $this->postHydrator->hydrate($post),
                $blog->getPosts()->toArray()
            );
            $blogResource->blogUsers = array_map(
                fn ($blogUser) => $this->blogUserHydrator->hydrate($blogUser),
                $blog->getBlogUsers()->toArray()
            );
            $blogResource->subscribed = $subscribed;

            return $blogResource;
        }, $rows);

        // Return a paginator to satisfy API Platform expectations
        return new ArrayPaginator($items, 0, $total);
    }
}
