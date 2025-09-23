<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\ApiPlatform\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Blog\Domain\Model\Post;
use App\Blog\Domain\Model\Subscription;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

readonly class PostSubscribedCurrentUserExtension implements QueryCollectionExtensionInterface
{
    public function __construct(private Security $security)
    {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if (Post::class !== $resourceClass || 'subscribedPosts' !== $operation->getName()) {
            return;
        }

        // Limit to GraphQL requests so REST remains unaffected
        if (empty($context['graphql'])) {
            return;
        }

        $user = $this->security->getUser();

        if (null === $user) {
            // If not authenticated, return empty set for this field
            $queryBuilder->andWhere('1 = 0');

            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        // Join blog and subscriptions, keep only subscriptions for current user
        $queryBuilder
            ->join(sprintf('%s.blog', $rootAlias), 'b')
            ->join(Subscription::class, 's', 'WITH', 's.blog = b AND s.subscriber = :currentUser')
            ->setParameter('currentUser', $user);
    }
}
