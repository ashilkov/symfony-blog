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
use App\Blog\API\Hydrator\BlogHydrator;
use App\Blog\API\Hydrator\BlogUserHydrator;
use App\Blog\Domain\Repository\BlogUserRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class CollectionProvider implements ProviderInterface
{
    public function __construct(
        private BlogUserRepositoryInterface $blogUserRepository,
        private BlogHydrator $blogHydrator,
        private BlogUserHydrator $blogUserHydrator,
        private Security $security,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $searchRequest = [];

        // Resolve pagination from context (defaults if not provided)
        $page = (int) ($context['filters']['page'] ?? 1);
        $itemsPerPage = (int) ($context['filters']['itemsPerPage'] ?? 30);
        $page = $page > 0 ? $page : 1;
        $itemsPerPage = $itemsPerPage > 0 ? $itemsPerPage : 30;
        $offset = ($page - 1) * $itemsPerPage;

        if (isset($uriVariables['blog_id'])) {
            $searchRequest['blog'] = $uriVariables['blog_id'];
        }
        $userId = $this->security->getUser()?->getId();
        if (null === $userId) {
            return null;
        }
        $searchRequest['userId'] = $userId;

        /** @var \App\Blog\Domain\Model\BlogUser|null $blogUser */
        $blogUsers = $this->blogUserRepository->findBy($searchRequest, null, $itemsPerPage, $offset);
        $total = $this->blogUserRepository->count([]);

        $items = array_map(function ($blogUser) {
            $blogUserResource = $this->blogUserHydrator->hydrate($blogUser);
            $blogUserResource->blog = $this->blogHydrator->hydrate($blogUser->getBlog());

            return $blogUserResource;
        }, $blogUsers);

        // Return a paginator to satisfy API Platform expectations
        return new ArrayPaginator($items, 0, $total);
    }
}
