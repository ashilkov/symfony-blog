<?php
/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Security\Blog;

use App\Blog\API\Resource\Blog;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class BlogPermissionVoter extends Voter
{
    const CREATE_BLOG = 'BLOG_CREATE_BLOG';
    const EDIT_BLOG = 'BLOG_EDIT_BLOG';
    const DELETE_BLOG = 'BLOG_DELETE_BLOG';

    public function __construct(
        private readonly BlogPermissionPolicy $policy,
        private readonly LoggerInterface      $logger,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        $this->logger->debug('supports', ['attribute' => $attribute, 'subject' => $subject]);

        return in_array($attribute, [self::CREATE_BLOG, self::EDIT_BLOG, self::DELETE_BLOG], true)
            && $subject instanceof Blog;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $userId = (int) $token->getUser()?->getId();
        if (!$userId) {
            return false;
        }

        /** @var Blog $blog */
        $blog = $subject;

        return match ($attribute) {
            self::CREATE_BLOG => $this->policy->canCreateBlog($userId),
            self::EDIT_BLOG => $this->policy->canEditBlog($userId, $blog),
            self::DELETE_BLOG => $this->policy->canDeleteBlog($userId, $blog),
            default => false,
        };
    }
}
