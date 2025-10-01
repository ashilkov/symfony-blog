<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Model;

class Post implements EntityInterface
{
    /**
     * @var Comment[]
     */
    private array $comments = [];

    private ?Blog $blog = null;

    public function __construct(
        private ?int $id = null,
        private ?string $title = null,
        private ?string $content = null,
        private ?int $userId = null,
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getBlog(): ?Blog
    {
        return $this->blog;
    }

    public function setBlog(?Blog $blog): static
    {
        $this->blog = $blog;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getComments(): iterable
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (isset($this->comments[$comment->getId()])) {
            return $this;
        }

        $this->comments[$comment->getId()] = $comment;
        $comment->setPost($this);

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if (!isset($this->comments[$comment->getId()])) {
            return $this;
        }

        unset($this->comments[$comment->getId()]);

        return $this;
    }
}
