<?php

namespace App\Blog\Domain\Repository;

use App\Blog\Domain\Model\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface CommentRepositoryInterface extends ServiceEntityRepositoryInterface, RepositoryInterface
{
    public function save(Comment $entity, bool $flush = false): void;

    public function remove(Comment $entity, bool $flush = false): void;
}
