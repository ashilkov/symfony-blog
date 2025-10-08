<?php

namespace App\Blog\Domain\Repository;

use App\Blog\Domain\Model\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface PostRepositoryInterface extends ServiceEntityRepositoryInterface, RepositoryInterface
{
    public function save(Post $entity, bool $flush = false): void;

    public function remove(Post $entity, bool $flush = false): void;
}
