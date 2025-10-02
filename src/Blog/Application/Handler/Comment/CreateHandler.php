<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Handler\Comment;

use App\Blog\Application\Command\Comment\CreateCommand;
use App\Blog\Domain\Model\Comment;
use App\Blog\Domain\Model\Post;
use App\Blog\Domain\Repository\CommentRepositoryInterface;
use App\Blog\Domain\Repository\PostRepositoryInterface;
use App\Blog\Domain\User\UserReadModelPortInterface;

readonly class CreateHandler
{
    public function __construct(
        private UserReadModelPortInterface $userReadModel,
        private CommentRepositoryInterface $commentRepository,
        private PostRepositoryInterface $postRepository,
    ) {
    }

    public function __invoke(CreateCommand $command): Comment
    {
        if (null === $command->userId) {
            throw new \LogicException('User is required to create a blog.');
        }

        $user = $this->userReadModel->findSummaryById($command->userId);
        if (null === $user) {
            throw new \LogicException('User is required to create a blog.');
        }

        /** @var Post $post */
        $post = $this->postRepository->find($command->postId);
        if (null === $post) {
            throw new \LogicException('Post is required to create a comment.');
        }

        $comment = new Comment();
        $comment->setPost($post);
        $comment->setUserId($command->userId);
        $comment->setContent($command->content);

        $this->commentRepository->save($comment, true);

        return $comment;
    }
}
