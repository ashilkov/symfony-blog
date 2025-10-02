<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\State\Comment;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\ArrayPaginator;
use ApiPlatform\State\ProviderInterface;
use App\Blog\API\Hydrator\CommentHydrator;
use App\Blog\Domain\Repository\CommentRepositoryInterface;

readonly class CollectionProvider implements ProviderInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private CommentHydrator $commentHydrator,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $postId = $uriVariables['post_id'] ?? null;
        if ($postId) {
            $comments = $this->commentRepository->findBy(['post' => $postId]);
        } else {
            $comments = $this->commentRepository->findBy([]);
        }

        $total = $this->commentRepository->count([]);
        $items = array_map(fn ($comment) => $this->commentHydrator->hydrate($comment), $comments);

        return new ArrayPaginator($items, 0, $total);
    }
}
