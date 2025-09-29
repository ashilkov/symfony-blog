<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Handler\Subscription;

use App\Blog\Application\Command\Subscription\CreateCommand;
use App\Blog\Domain\Model\Subscription;
use App\Blog\Domain\Repository\BlogRepositoryInterface;
use App\Blog\Domain\Repository\SubscriptionRepositoryInterface;
use App\Blog\Domain\User\UserReadModelPortInterface;

readonly class CreateHandler
{
    public function __construct(
        private BlogRepositoryInterface $blogRepository,
        private UserReadModelPortInterface $userReadModel,
        private SubscriptionRepositoryInterface $subscriptionRepository,
    ) {
    }

    public function __invoke(CreateCommand $command): Subscription
    {
        if (null === $command->subscriberId) {
            throw new \LogicException('User is required to subscribe to a subscription.');
        }

        $user = $this->userReadModel->findSummaryById($command->subscriberId);
        if (null === $user) {
            throw new \LogicException('User is required to subscribe to a subscription.');
        }

        $subscription = new Subscription();
        $subscription->setSubscriberId($command->subscriberId);

        if (null === $command->blogId) {
            throw new \LogicException('Blog is required to subscribe.');
        }

        $blog = $this->blogRepository->find($command->blogId);
        $subscription->setBlog($blog);

        $this->subscriptionRepository->save($subscription, true);

        return $subscription;
    }
}
