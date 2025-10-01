<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Doctrine\Entity;

use App\Blog\Domain\Model\Comment;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Post implements DoctrineEntityInterface, TimestampableInterface
{
    /**
     * @phpstan-ignore-next-line Doctrine sets the ID at runtime
     */
    public ?int $id = null;

    public ?string $content = null;

    public ?int $userId = null;

    public ?Blog $blog = null;

    public ?string $title = null;

    public ?\DateTimeImmutable $createdAt = null;

    public ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Comment>
     */
    public Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
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
