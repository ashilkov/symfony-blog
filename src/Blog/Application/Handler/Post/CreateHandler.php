<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Handler\Post;

use App\Blog\Application\Command\Post\CreateCommand;
use App\Blog\Domain\Model\Blog;
use App\Blog\Domain\Model\Post;
use App\Blog\Domain\Repository\BlogRepositoryInterface;
use App\Blog\Domain\Repository\PostRepositoryInterface;
use App\Blog\Domain\User\UserReadModelPortInterface;

readonly class CreateHandler
{
    public function __construct(
        private PostRepositoryInterface $posts,
        private UserReadModelPortInterface $userReadModel,
        private BlogRepositoryInterface $blogs,
    ) {
    }

    public function __invoke(CreateCommand $command): Post
    {
        $post = new Post();

        if (null === $command->userId) {
            throw new \LogicException('User is required to create a blog.');
        }

        $user = $this->userReadModel->findSummaryById($command->userId);
        if (null === $user) {
            throw new \LogicException('User is required to create a blog.');
        }

        /** @var Blog $blog */
        $blog = $this->blogs->find($command->blogId);
        if (null === $blog) {
            throw new \LogicException('Blog is required to create a post.');
        }

        $post->setUserId($user->id);

        if (null !== $command->title) {
            $post->setTitle($command->title);
        }
        if (null !== $command->content) {
            $post->setContent($command->content);
        }
        $post->setBlog($blog);

        $this->posts->save($post, true);

        return $post;
    }
}
