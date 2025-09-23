<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Security;

use App\Blog\Domain\Model\Post;
use App\User\Domain\Model\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Post>
 */
class BlogPermissionVoter extends Voter
{
    public const CREATE_POST = 'BLOG_CREATE_POST';
    public const EDIT_POST = 'BLOG_EDIT_POST';
    public const DELETE_POST = 'BLOG_DELETE_POST';

    public function __construct(private readonly BlogPermissionPolicy $policy, private readonly LoggerInterface $logger)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        $this->logger->debug('supports', ['attribute' => $attribute, 'subject' => $subject]);

        return in_array($attribute, [self::CREATE_POST, self::EDIT_POST, self::DELETE_POST], true)
            && $subject instanceof Post;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var Post $post */
        $post = $subject;

        return match ($attribute) {
            self::CREATE_POST => $post->getBlog() && $this->policy->canCreatePost($user, $post->getBlog()),
            self::EDIT_POST => $this->policy->canEditPost($user, $post),
            self::DELETE_POST => $this->policy->canDeletePost($user, $post),
            default => false,
        };
    }
}
