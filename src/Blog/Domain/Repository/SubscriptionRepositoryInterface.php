<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Repository;

use App\Blog\Domain\Model\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface SubscriptionRepositoryInterface extends ServiceEntityRepositoryInterface, RepositoryInterface
{
    public function save(Subscription $entity, bool $flush = false): void;

    public function remove(Subscription $entity, bool $flush = false): void;
}
