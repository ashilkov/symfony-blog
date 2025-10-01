<?php

namespace App\Blog\Infrastructure\Doctrine\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Blog implements DoctrineEntityInterface, TimestampableInterface
{
    public ?int $id = null;

    public string $name;

    public string $description;

    public ?\DateTimeImmutable $createdAt = null;

    public ?\DateTimeImmutable $updatedAt = null;

    /** @var Collection<int, Post> */
    public Collection $posts;

    /** @var Collection<int, BlogUser> */
    public Collection $blogUsers;

    /** @var Collection<int, Subscription> */
    public Collection $subscriptions;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->blogUsers = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
