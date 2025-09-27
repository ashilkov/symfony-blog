<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Handler\Blog;

use App\Blog\Application\Command\Blog\CreateCommand;
use App\Blog\Domain\Event\BeforeCreateEvent;
use App\Blog\Domain\Model\Blog;
use App\Blog\Domain\Repository\BlogRepositoryInterface;
use App\Blog\Domain\User\UserReadModelPortInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

readonly class CreateHandler
{
    public function __construct(
        private BlogRepositoryInterface $blogs,
        private UserReadModelPortInterface $userReadModel,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(CreateCommand $command): Blog
    {
        if (null === $command->userId) {
            throw new \LogicException('User is required to create a blog.');
        }

        $user = $this->userReadModel->findSummaryById($command->userId);
        if (null === $user) {
            throw new \LogicException('User is required to create a blog.');
        }

        $blog = new Blog();
        if (null !== $command->name) {
            $blog->setName($command->name);
        }
        if (null !== $command->description) {
            $blog->setDescription($command->description);
        }

        $this->eventDispatcher->dispatch(new BeforeCreateEvent($blog, $user));

        $this->blogs->save($blog, true);

        return $blog;
    }
}
