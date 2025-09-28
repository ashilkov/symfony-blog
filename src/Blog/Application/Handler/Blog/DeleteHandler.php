<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Handler\Blog;

use App\Blog\Application\Command\Blog\DeleteCommand;
use App\Blog\Domain\Model\Blog;
use App\Blog\Domain\Repository\BlogRepositoryInterface;

readonly class DeleteHandler
{
    public function __construct(
        private BlogRepositoryInterface $blogs,
    ) {
    }

    public function __invoke(DeleteCommand $command): void
    {
        /** @var Blog|null $blog */
        $blog = $this->blogs->findOneBy(['id' => $command->id]);
        if (null === $blog) {
            throw new \RuntimeException('Blog not found.');
        }

        $this->blogs->remove($blog, true);
    }
}
