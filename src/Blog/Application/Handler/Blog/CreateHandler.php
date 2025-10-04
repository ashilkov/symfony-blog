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
use App\Blog\Domain\Factory\BlogFactory;
use App\Blog\Domain\Model\Blog;
use App\Blog\Domain\Repository\BlogRepositoryInterface;
use App\Blog\Domain\User\UserReadModelPortInterface;
use App\Blog\Domain\Value\Blog\BlogDescription;
use App\Blog\Domain\Value\Blog\BlogName;
use Psr\EventDispatcher\EventDispatcherInterface;

readonly class CreateHandler
{
    public function __construct(
        private BlogRepositoryInterface $blogs,
        private UserReadModelPortInterface $userReadModel,
        private EventDispatcherInterface $eventDispatcher,
        private BlogFactory $blogFactory,
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

        // Ensure required fields are provided and construct Value Objects
        if (null === $command->name) {
            throw new \LogicException('Blog name is required.');
        }
        if (null === $command->description) {
            throw new \LogicException('Blog description is required.');
        }

        $blogName = new BlogName($command->name);
        $blogDescription = new BlogDescription($command->description);

        // Create the Blog aggregate via factory with Value Objects
        $blog = $this->blogFactory->create($blogName, $blogDescription);

        $this->eventDispatcher->dispatch(new BeforeCreateEvent($blog, $user));

        $this->blogs->save($blog, true);

        return $blog;
    }
}
