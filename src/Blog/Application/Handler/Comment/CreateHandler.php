<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Handler\Comment;

use App\Blog\Application\Command\Comment\CreateCommand;
use App\Blog\Domain\Factory\CommentFactory;
use App\Blog\Domain\Model\Comment;
use App\Blog\Domain\Model\Post;
use App\Blog\Domain\Repository\CommentRepositoryInterface;
use App\Blog\Domain\Repository\PostRepositoryInterface;
use App\Blog\Domain\User\UserReadModelPortInterface;
use App\Blog\Domain\Value\Common\Content;
use App\Blog\Domain\Value\Common\UserId;
use App\Blog\Domain\Value\Post\PostId;

readonly class CreateHandler
{
    public function __construct(
        private UserReadModelPortInterface $userReadModel,
        private CommentRepositoryInterface $commentRepository,
        private PostRepositoryInterface $postRepository,
        private CommentFactory $commentFactory,
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

        $comment = $this->commentFactory->create(
            new Content($command->content),
            new UserId($command->userId),
            new PostId($command->postId),
        );

        $this->commentRepository->save($comment, true);

        return $comment;
    }
}
