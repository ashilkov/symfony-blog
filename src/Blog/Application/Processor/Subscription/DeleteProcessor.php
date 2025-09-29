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
use App\Blog\Application\Handler\Subscription\DeleteHandler;
use App\Blog\Domain\Repository\SubscriptionRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class DeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private DeleteHandler $handler,
        private Security $security,
        private SubscriptionHydrator $hydrator,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Subscription
    {
        $user = $this->security->getUser();
        if (null === $user) {
            throw new AccessDeniedException('You are not allowed to unsubscribe from a blog.');
        }

        $blogId = $uriVariables['blog_id']
            ?? ($data->blogId ?? null)
            ?? ($context['args']['input']['blogId'] ?? null);

        if (null === $blogId) {
            throw new \InvalidArgumentException('Blog ID is required for Subscription delete.');
        }

        $subscription = $this->subscriptionRepository->findOneBy([
            'blog' => $blogId,
            'subscriberId' => $user->getId(),
        ]);
        if (null === $subscription) {
            throw new \InvalidArgumentException('Subscription not found for the given blog and user.');
        }

        // If you hydrate and return this resource, GraphQL won’t need to read after deletion.
        $resource = $this->hydrator->hydrate($subscription);

        ($this->handler)(new DeleteCommand((int) $subscription->getId()));

        return $resource;
    }
}
