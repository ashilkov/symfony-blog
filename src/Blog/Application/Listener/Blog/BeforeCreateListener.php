<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Listener\Blog;

use App\Blog\Domain\Enum\BlogUserRole;
use App\Blog\Domain\Event\BeforeCreateEvent;
use App\Blog\Domain\Model\BlogUser;
use App\Blog\Domain\User\UserReadModelPortInterface;

class BeforeCreateListener
{
    public function __construct(
        private UserReadModelPortInterface $userReadModelPort,
    ) {
    }

    public function __invoke(BeforeCreateEvent $event): void
    {
        $blog = $event->blog;

        foreach ($blog->getBlogUsers() as $blogUser) {
            if ($blogUser->getUserId() === $event->user->id) {
                return;
            }
        }

        $user = $this->userReadModelPort->findSummaryById($event->user->id);
        if (null === $user) {
            throw new \LogicException('User is required to create a blog.');
        }

        $blogUser = new BlogUser($blog, $event->user->id, BlogUserRole::ROLE_ADMIN);
        $blog->addBlogUser($blogUser);
    }
}
