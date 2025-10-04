<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\State\BlogUser;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\ArrayPaginator;
use ApiPlatform\State\ProviderInterface;
use App\Blog\API\Hydrator\BlogUserHydrator;
use App\Blog\Application\CurrentUserProviderInterface;
use App\Blog\Domain\Repository\BlogUserRepositoryInterface;

readonly class CollectionProvider implements ProviderInterface
{
    public function __construct(
        private BlogUserRepositoryInterface $blogUserRepository,
        private BlogUserHydrator $blogUserHydrator,
        private CurrentUserProviderInterface $userProvider,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (isset($uriVariables['blog_id'])) {
            $searchRequest['blog'] = $uriVariables['blog_id'];
        }

        $userId = $this->userProvider->getUserId();
        if (null === $userId) {
            return null;
        }
        $searchRequest['userId'] = $userId;

        /** @var \App\Blog\Domain\Model\BlogUser|null $blogUser */
        $blogUsers = $this->blogUserRepository->findBy($searchRequest);
        $total = $this->blogUserRepository->count([]);

        $items = array_map(fn ($blogUser) => $this->blogUserHydrator->hydrate($blogUser), $blogUsers);

        // Return a paginator to satisfy API Platform expectations
        return new ArrayPaginator($items, 0, $total);
    }
}
