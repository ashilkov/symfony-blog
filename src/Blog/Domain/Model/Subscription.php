<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Model;

use App\Blog\Domain\Value\Blog\BlogId;
use App\Blog\Domain\Value\Common\UserId;
use App\Blog\Domain\Value\Subscription\SubscriptionId;

class Subscription implements EntityInterface
{
    public function __construct(
        private ?SubscriptionId $id = null,
        private ?BlogId $blogId = null,
        private ?UserId $subscriberId = null,
        private ?\DateTimeImmutable $createdAt = new \DateTimeImmutable(),
        private ?\DateTimeImmutable $updatedAt = new \DateTimeImmutable(),
    ) {
    }

    public function assignId(SubscriptionId $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?SubscriptionId
    {
        return $this->id;
    }

    public function getBlogId(): ?BlogId
    {
        return $this->blogId;
    }

    public function attachToBlog(BlogId $blogId): static
    {
        $this->blogId = $blogId;
        $this->touch();

        return $this;
    }

    public function getSubscriberId(): ?UserId
    {
        return $this->subscriberId;
    }

    public function assignSubscriber(UserId $subscriberId): static
    {
        $this->subscriberId = $subscriberId;
        $this->touch();

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt ?? new \DateTimeImmutable();
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt ?? new \DateTimeImmutable();
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
