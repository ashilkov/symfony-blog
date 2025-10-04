<?php

namespace App\Blog\Domain\Model;

use App\Blog\Domain\Value\Comment\CommentId;
use App\Blog\Domain\Value\Common\Content;
use App\Blog\Domain\Value\Common\UserId;
use App\Blog\Domain\Value\Post\PostId;

class Comment implements EntityInterface
{
    public function __construct(
        private ?CommentId $id = null,
        private ?UserId $userId = null,
        private ?Content $content = null,
        private ?PostId $postId = null,
        private ?\DateTimeImmutable $createdAt = new \DateTimeImmutable(),
        private ?\DateTimeImmutable $updatedAt = new \DateTimeImmutable(),
    ) {
    }

    public function assignId(CommentId $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?CommentId
    {
        return $this->id;
    }

    public function getUserId(): ?UserId
    {
        return $this->userId;
    }

    public function assignUser(UserId $userId): self
    {
        $this->userId = $userId;
        $this->touch();

        return $this;
    }

    public function getContent(): ?Content
    {
        return $this->content;
    }

    public function changeContent(Content $content): self
    {
        $this->content = $content;
        $this->touch();

        return $this;
    }

    public function getPostId(): ?PostId
    {
        return $this->postId;
    }

    public function attachToPost(?PostId $postId): self
    {
        $this->postId = $postId;
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
