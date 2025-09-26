<?php

namespace App\Blog\Domain\Repository;

use App\Blog\Domain\Model\Blog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface BlogRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function save(Blog $entity, bool $flush = false): void;
}
