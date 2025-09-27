<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\State\BlogUser;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Blog\API\Hydrator\BlogHydrator;
use App\Blog\API\Hydrator\BlogUserHydrator;
use App\Blog\API\Resource\BlogUser as BlogUserResource;
use App\Blog\Domain\Model\BlogUser;
use App\Blog\Domain\Repository\BlogUserRepositoryInterface;

readonly class ItemProvider implements ProviderInterface
{
    public function __construct(
        private BlogUserRepositoryInterface $blogUserRepository,
        private BlogUserHydrator $blogUserHydrator,
        private BlogHydrator $blogHydrator,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?BlogUserResource
    {
        $blogId = $uriVariables['blog_id'] ?? $uriVariables['blogId'] ?? 0;
        $userId = $uriVariables['user_id'] ?? $uriVariables['userId'] ?? 0;

        if (null === $blogId || null === $userId) {
            return null;
        }

        /** @var BlogUser|null $blogUser */
        $blogUser = $this->blogUserRepository->findOneBy([
            'blog' => (int) $blogId,
            'userId' => (int) $userId,
        ]);

        if (null === $blogUser) {
            return null;
        }

        $blogUserResource = $this->blogUserHydrator->hydrate($blogUser);
        $blogUserResource->blog = $this->blogHydrator->hydrate($blogUser->getBlog());

        return $blogUserResource;
    }
}
