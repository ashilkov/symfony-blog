<?php

namespace App\Blog\Domain\Repository;

use App\Blog\Domain\Model\Blog;
use App\Blog\Domain\Model\EntityInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface BlogRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function save(Blog $entity, bool $flush = false): void;

    public function remove(Blog $entity, bool $flush = false): void;
}
