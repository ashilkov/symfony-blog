<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Handler\Subscription;

use App\Blog\Application\Command\Subscription\DeleteCommand;
use App\Blog\Domain\Model\Subscription;
use App\Blog\Domain\Repository\SubscriptionRepositoryInterface;

readonly class DeleteHandler
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
    ) {
    }

    public function __invoke(DeleteCommand $command): void
    {
        /** @var Subscription|null $subscription */
        $subscription = $this->subscriptionRepository->findOneBy(['id' => $command->id]);
        if (null === $subscription) {
            throw new \RuntimeException('Subscription not found.');
        }

        $this->subscriptionRepository->remove($subscription, true);
    }
}
