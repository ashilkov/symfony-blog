<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Model;

use App\Blog\Domain\Value\Blog\BlogDescription;
use App\Blog\Domain\Value\Blog\BlogId;
use App\Blog\Domain\Value\Blog\BlogName;

class Blog implements EntityInterface
{
    /**
     * @var BlogUser[]
     */
    private array $blogUsers = [];

    /**
     * @var Post[]
     */
    private array $posts = [];

    /**
     * @var Subscription[]
     */
    private array $subscriptions = [];

    public function __construct(
        private ?BlogId $id = null,
        private ?BlogName $name = null,
        private ?BlogDescription $description = null,
        private ?\DateTimeImmutable $createdAt = new \DateTimeImmutable(),
        private ?\DateTimeImmutable $updatedAt = new \DateTimeImmutable(),
    ) {
    }

    public function assignId(?BlogId $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?BlogId
    {
        return $this->id;
    }

    public function getName(): ?BlogName
    {
        return $this->name;
    }

    public function rename(BlogName $name): static
    {
        if (!$this->name->equals($name)) {
            $this->name = $name;
            $this->touch();
        }

        return $this;
    }

    public function getDescription(): ?BlogDescription
    {
        return $this->description;
    }

    public function setDescription(BlogDescription $description): static
    {
        $isChanged = ($this->description?->value() ?? null) !== ($description?->value() ?? null);
        if ($isChanged) {
            $this->description = $description;
            $this->touch();
        }

        return $this;
    }

    /** @return iterable<BlogUser> */
    public function getBlogUsers(): iterable
    {
        return $this->blogUsers;
    }

    public function addBlogUser(BlogUser $blogUser): self
    {
        if (isset($this->blogUsers[$blogUser->getUserId()->value()])) {
            return $this;
        }
        if (null !== $this->getId()) {
            $blogUser->attachToBlog($this->getId());
        }
        $this->blogUsers[$blogUser->getUserId()->value()] = $blogUser;

        return $this;
    }

    public function removeBlogUser(BlogUser $blogUser): self
    {
        if (!isset($this->blogUsers[$blogUser->getUserId()->value()])) {
            return $this;
        }
        unset($this->blogUsers[$blogUser->getUserId()]);

        return $this;
    }

    /** @return iterable<Post> */
    public function getPosts(): iterable
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (isset($this->posts[$post->getId()->value()])) {
            return $this;
        }
        $this->posts[$post->getId()->value()] = $post;
        if (null !== $this->getId()) {
            $post->attachToBlog($this->getId());
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if (!isset($this->posts[$post->getId()->value()])) {
            return $this;
        }
        unset($this->posts[$post->getId()->value()]);

        return $this;
    }

    /** @return iterable<Subscription> */
    public function getSubscriptions(): iterable
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): static
    {
        if (isset($this->subscriptions[$subscription->getId()->value()])) {
            return $this;
        }
        $subscription->attachToBlog($this->getId());
        $this->subscriptions[$subscription->getId()->value()] = $subscription;

        return $this;
    }

    public function removeSubscription(Subscription $subscription): static
    {
        if (!isset($this->subscriptions[$subscription->getId()->value()])) {
            return $this;
        }
        unset($this->subscriptions[$subscription->getId()->value()]);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
