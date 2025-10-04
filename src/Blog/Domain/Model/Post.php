<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Model;

use App\Blog\Domain\Value\Blog\BlogId;
use App\Blog\Domain\Value\Common\Content;
use App\Blog\Domain\Value\Common\UserId;
use App\Blog\Domain\Value\Post\PostId;
use App\Blog\Domain\Value\Post\PostTitle;

class Post implements EntityInterface
{
    /**
     * @var Comment[]
     */
    private array $comments = [];

    public function __construct(
        private ?PostId $id = null,
        private ?PostTitle $title = null,
        private ?Content $content = null,
        private ?UserId $userId = null,
        private ?BlogId $blogId = null,
        private ?\DateTimeImmutable $createdAt = new \DateTimeImmutable(),
        private ?\DateTimeImmutable $updatedAt = new \DateTimeImmutable(),
    ) {
    }

    public function assignId(PostId $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?PostId
    {
        return $this->id;
    }

    public function getContent(): ?Content
    {
        return $this->content;
    }

    public function changeContent(Content $content): static
    {
        $this->content = $content;
        $this->touch();

        return $this;
    }

    public function getAuthorId(): ?UserId
    {
        return $this->userId;
    }

    public function assignAuthor(UserId $userId): static
    {
        $this->userId = $userId;
        $this->touch();

        return $this;
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

    public function getTitle(): ?PostTitle
    {
        return $this->title;
    }

    public function rename(PostTitle $title): static
    {
        if (true !== $this->title?->equals($title)) {
            $this->title = $title;
            $this->touch();
        }

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

    /**
     * @return iterable<Comment>
     */
    public function getComments(): iterable
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        $key = (string) $comment->getId();
        if (isset($this->comments[$key])) {
            return $this;
        }

        $this->comments[$key] = $comment;
        $comment->attachToPost($this->getId());
        $this->touch();

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        $key = (string) $comment->getId();
        if (!isset($this->comments[$key])) {
            return $this;
        }

        unset($this->comments[$key]);
        $this->touch();

        return $this;
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
