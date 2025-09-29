<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Model;

use App\Blog\Domain\Repository\SubscriptionRepositoryInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(
    name: 'subscription',
    uniqueConstraints: [
        new ORM\UniqueConstraint(
            name: 'UNIQ_SUBSCRIPTION_BLOG_USER',
            columns: ['blog_id', 'user_id']
        ),
    ]
)]
#[ORM\Entity(repositoryClass: SubscriptionRepositoryInterface::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(['user_id', 'blog_id'], message: 'You are already subscribed to this blog')]
class Subscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @phpstan-ignore-next-line Doctrine sets the ID at runtime
     */
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'subscriptions')]
    #[ORM\JoinColumn(name: 'blog_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Blog $blog = null;

    #[ORM\Column(name: 'user_id', type: 'integer', nullable: false)]
    private ?int $subscriberId = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSubscriberId(): ?int
    {
        return $this->subscriberId;
    }

    public function setSubscriberId(?int $subscriberId): static
    {
        $this->subscriberId = $subscriberId;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    #[ORM\PrePersist]
    public function setTimestampsOnCreate(): void
    {
        $now = new \DateTimeImmutable();
        if (null === $this->created_at) {
            $this->created_at = $now;
        }
        $this->updated_at = $now;
    }

    #[ORM\PreUpdate]
    public function setTimestampOnUpdate(): void
    {
        $this->updated_at = new \DateTimeImmutable();
    }
}
