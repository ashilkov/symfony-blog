<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Handler\Blog;

use App\Blog\Application\Command\Blog\UpdateCommand;
use App\Blog\Domain\Model\Blog;
use App\Blog\Domain\Repository\BlogRepositoryInterface;
use App\Blog\Domain\Value\Blog\BlogDescription;
use App\Blog\Domain\Value\Blog\BlogName;

readonly class UpdateHandler
{
    public function __construct(
        private BlogRepositoryInterface $blogs,
    ) {
    }

    public function __invoke(UpdateCommand $command): Blog
    {
        /** @var Blog|null $blog */
        $blog = $this->blogs->findOneBy(['id' => $command->id]);
        if (null === $blog) {
            throw new \RuntimeException('Blog not found.');
        }

        if (null !== $command->name) {
            $blog->rename(new BlogName($command->name));
        }
        if (null !== $command->description) {
            $blog->setDescription(new BlogDescription($command->description));
        }

        $this->blogs->save($blog, true);

        return $blog;
    }
}
