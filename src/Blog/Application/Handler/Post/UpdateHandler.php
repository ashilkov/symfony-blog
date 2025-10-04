<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Handler\Post;

use App\Blog\Application\Command\Post\UpdateCommand;
use App\Blog\Domain\Model\Post;
use App\Blog\Domain\Repository\PostRepositoryInterface;
use App\Blog\Domain\Value\Common\Content;
use App\Blog\Domain\Value\Post\PostTitle;

readonly class UpdateHandler
{
    public function __construct(
        private PostRepositoryInterface $posts,
    ) {
    }

    public function __invoke(UpdateCommand $command): Post
    {
        /** @var Post|null $post */
        $post = $this->posts->findOneBy(['id' => $command->id]);
        if (null === $post) {
            throw new \RuntimeException('Blog not found.');
        }

        if (null !== $command->title) {
            $post->rename(new PostTitle($command->title));
        }
        if (null !== $command->content) {
            $post->changeContent(new Content($command->content));
        }

        $this->posts->save($post, true);

        return $post;
    }
}
