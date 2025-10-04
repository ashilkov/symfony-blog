<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Security\Post;

use App\Blog\API\Resource\Post;
use App\Blog\Domain\Enum\Actions;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

readonly class AllowedActionsResolver
{
    public function __construct(private AuthorizationCheckerInterface $auth)
    {
    }

    /**
     * @return string[]
     */
    public function resolve(Post $post): array
    {
        $actions = [];

        // If we returned the post, we consider it viewable
        $actions[] = Actions::VIEW;

        if ($this->auth->isGranted('BLOG_EDIT_POST', $post)) {
            $actions[] = Actions::EDIT;
        }
        if ($this->auth->isGranted('BLOG_DELETE_POST', $post)) {
            $actions[] = Actions::DELETE;
        }

        return $actions;
    }
}
