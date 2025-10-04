<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Processor\Subscription;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Blog\API\Hydrator\SubscriptionHydrator;
use App\Blog\API\Resource\Subscription;
use App\Blog\Application\Command\Subscription\DeleteCommand;
use App\Blog\Application\CurrentUserProviderInterface;
use App\Blog\Application\Handler\Subscription\DeleteHandler;
use App\Blog\Domain\Repository\SubscriptionRepositoryInterface;

readonly class DeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private DeleteHandler $handler,
        private SubscriptionHydrator $hydrator,
        private CurrentUserProviderInterface $currentUserProvider,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Subscription
    {
        $userId = $this->currentUserProvider->getUserId();
        if (null === $userId) {
            throw new AccessDeniedException('You are not allowed to unsubscribe from a blog.');
        }

        $blogId = $uriVariables['blog_id'] ?? ($data->blogId ?? null);

        if (null === $blogId) {
            throw new \InvalidArgumentException('Blog ID is required for Subscription delete.');
        }
        /** @var \App\Blog\Domain\Model\Subscription $subscription */
        $subscription = $this->subscriptionRepository->findOneBy([
            'blog' => $blogId,
            'subscriberId' => $userId,
        ]);
        if (null === $subscription) {
            throw new \InvalidArgumentException('Subscription not found for the given blog and user.');
        }

        // If you hydrate and return this resource, GraphQL won’t need to read after deletion.
        $resource = $this->hydrator->hydrate($subscription);

        ($this->handler)(new DeleteCommand((int) $subscription->getId()->value()));

        return $resource;
    }
}
