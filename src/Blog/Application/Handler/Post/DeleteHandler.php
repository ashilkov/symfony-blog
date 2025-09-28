<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Handler\Post;

use App\Blog\Application\Command\Post\DeleteCommand;
use App\Blog\Domain\Model\Post;
use App\Blog\Domain\Repository\PostRepositoryInterface;

readonly class DeleteHandler
{
    public function __construct(
        private PostRepositoryInterface $posts,
    ) {
    }

    public function __invoke(DeleteCommand $command): void
    {
        /** @var Post|null $post */
        $post = $this->posts->findOneBy(['id' => $command->id]);
        if (null === $post) {
            throw new \RuntimeException('Post not found.');
        }

        $this->posts->remove($post, true);
    }
}
