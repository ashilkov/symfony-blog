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
use App\Blog\Domain\Model\BlogUser;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

readonly class BlogUserCurrentUserExtension implements QueryCollectionExtensionInterface
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

        if (BlogUser::class !== $resourceClass) {
            return;
        }

        // Limit to GraphQL requests so REST remains unaffected
        if (empty($context['graphql'])) {
            return;
        }

        $user = $this->security->getUser();
        $rootAlias = $queryBuilder->getRootAliases()[0];

        if (null === $user) {
            // If not authenticated, return empty set for this field
            $queryBuilder->andWhere('1 = 0');

            return;
        }

        // Keep the subresource constraints built by API Platform and add the user constraint
        $queryBuilder
            ->andWhere(sprintf('%s.user = :currentUser', $rootAlias))
            ->setParameter('currentUser', $user);
    }
}
