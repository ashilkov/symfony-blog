<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Repository;

use App\Blog\Domain\Repository\SubscriptionRepositoryInterface;
use App\Blog\Infrastructure\Doctrine\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Subscription>
 */
class SubscriptionRepository extends AbstractRepository implements SubscriptionRepositoryInterface
{
    public function getEntityType(): string
    {
        return Subscription::class;
    }
}
