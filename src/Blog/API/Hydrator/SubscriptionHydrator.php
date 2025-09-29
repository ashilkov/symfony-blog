<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\Hydrator;

use App\Blog\API\Resource\Subscription as SubscriptionResource;
use App\Blog\Domain\Model\Subscription;

class SubscriptionHydrator
{
    public function hydrate(Subscription $subscription): SubscriptionResource
    {
        return new SubscriptionResource(
            id: $subscription->getId(),
            blogId: $subscription->getBlog()->getId(),
            subscriberId: $subscription->getSubscriberId()
        );
    }
}
