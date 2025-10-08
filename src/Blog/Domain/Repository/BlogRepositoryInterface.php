<?php

namespace App\Blog\Domain\Repository;

use App\Blog\Domain\Model\Blog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

/**
 * @extends ServiceEntityRepositoryInterface<Blog>
 */
interface BlogRepositoryInterface extends ServiceEntityRepositoryInterface, RepositoryInterface
{
    public function save(Blog $entity, bool $flush = false): void;

    public function remove(Blog $entity, bool $flush = false): void;
}
