<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Security;

use App\Blog\Application\CurrentUserProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class CurrentUserProvider implements CurrentUserProviderInterface
{
    public function __construct(private Security $security)
    {
    }

    public function getUserId(): ?int
    {
        $user = $this->security->getUser();

        if (null === $user) {
            return null;
        }

        return method_exists($user, 'getId') ? (int) $user->getId() : null;
    }
}
