<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Security\Post;

use App\Blog\API\Resource\Post;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Post>
 */
class PostPermissionVoter extends Voter
{
    public const CREATE_POST = 'BLOG_CREATE_POST';
    public const EDIT_POST = 'BLOG_EDIT_POST';
    public const DELETE_POST = 'BLOG_DELETE_POST';

    public function __construct(
        private readonly PostPermissionPolicy $policy,
        private readonly LoggerInterface $logger,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        $this->logger->debug('supports', ['attribute' => $attribute, 'subject' => $subject]);

        return in_array($attribute, [self::CREATE_POST, self::EDIT_POST, self::DELETE_POST], true)
            && $subject instanceof Post;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $userId = (int) $token->getUser()?->getId();
        if (!$userId) {
            return false;
        }

        /** @var Post $post */
        $post = $subject;

        return match ($attribute) {
            self::CREATE_POST => $post->blog && $this->policy->canCreatePost($userId, $post->blog),
            self::EDIT_POST => $this->policy->canEditPost($userId, $post),
            self::DELETE_POST => $this->policy->canDeletePost($userId, $post),
            default => false,
        };
    }
}
