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
use App\Blog\Domain\Value\Common\UserId;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: BeforeCreateEvent::class, method: '__invoke')]
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

        $blogUser = new BlogUser(
            userId: new UserId($event->user->id),
            role: BlogUserRole::ROLE_ADMIN,
            blogId: $blog->getId()
        );

        $blog->addBlogUser($blogUser);
    }
}
