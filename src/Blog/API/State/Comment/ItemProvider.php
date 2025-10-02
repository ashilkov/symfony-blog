<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\State\Comment;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Blog\API\Hydrator\CommentHydrator;
use App\Blog\Domain\Model\Comment;
use App\Blog\Domain\Repository\CommentRepositoryInterface;

readonly class ItemProvider implements ProviderInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private CommentHydrator $commentHydrator,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $id = $uriVariables['comment_id'] ?? $uriVariables['id'] ?? null;
        if (null === $id) {
            return null;
        }

        /** @var Comment $comment */
        $comment = $this->commentRepository->findOneBy(['id' => (int) $id]);
        if (null === $comment) {
            return null;
        }

        return $this->commentHydrator->hydrate($comment);
    }
}
