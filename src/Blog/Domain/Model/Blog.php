<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Model;

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
        private ?int $id = null,
        private ?string $name = null,
        private ?string $description = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null,
    ) {
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getBlogUsers(): iterable
    {
        return $this->blogUsers;
    }

    public function addBlogUser(BlogUser $blogUser): self
    {
        if (isset($this->blogUsers[$blogUser->getUserId()])) {
            return $this;
        }
        $blogUser->setBlog($this);
        $this->blogUsers[$blogUser->getUserId()] = $blogUser;

        return $this;
    }

    public function removeBlogUser(BlogUser $blogUser): self
    {
        if (!isset($this->blogUsers[$blogUser->getUserId()])) {
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
        if (isset($this->posts[$post->getId()])) {
            return $this;
        }
        $this->posts[$post->getId()] = $post;
        $post->setBlog($this);

        return $this;
    }

    public function removePost(Post $post): static
    {
        if (!isset($this->posts[$post->getId()])) {
            return $this;
        }
        unset($this->posts[$post->getId()]);

        return $this;
    }

    /** @return iterable<Subscription> */
    public function getSubscriptions(): iterable
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): static
    {
        if (isset($this->subscriptions[$subscription->getId()])) {
            return $this;
        }
        $subscription->setBlog($this);
        $this->subscriptions[$subscription->getId()] = $subscription;

        return $this;
    }

    public function removeSubscription(Subscription $subscription): static
    {
        if (!isset($this->subscriptions[$subscription->getId()])) {
            return $this;
        }
        unset($this->subscriptions[$subscription->getId()]);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
