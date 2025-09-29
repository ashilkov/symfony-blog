<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\State\Subscription;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Blog\API\Hydrator\SubscriptionHydrator;
use App\Blog\Domain\Model\Subscription;
use App\Blog\Domain\Repository\SubscriptionRepositoryInterface;

readonly class ItemProvider implements ProviderInterface
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private SubscriptionHydrator $subscriptionHydrator,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?\App\Blog\API\Resource\Subscription
    {
        $id = $uriVariables['subscription_id'] ?? $uriVariables['id'] ?? null;
        if (null === $id) {
            return null;
        }

        /** @var Subscription $subscription */
        $subscription = $this->subscriptionRepository->findOneBy(['id' => (int) $id]);
        if (null === $subscription) {
            return null;
        }

        return $this->subscriptionHydrator->hydrate($subscription);
    }
}
