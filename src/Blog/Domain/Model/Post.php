<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Model;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use App\Blog\Application\Processor\PostCreateProcessor;
use App\Blog\Infrastructure\Repository\PostRepository;
use App\User\Domain\Model\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(
    operations: [],
    normalizationContext: ['groups' => ['post:read'], 'iri_only' => false, 'enable_max_depth' => true],
    denormalizationContext: ['groups' => ['post:write']],
    graphQlOperations: [
        new QueryCollection(),
        new Query(),
        new Mutation(
            securityPostDenormalize: "is_granted('BLOG_CREATE_POST', object)",
            name: 'create',
            processor: PostCreateProcessor::class
        ),
        new Mutation(
            security: 'is_granted("BLOG_EDIT_POST", object)',
            name: 'edit',
        ),
        new Mutation(
            security: 'is_granted("BLOG_DELETE_POST", object)',
            name: 'delete',
        ),
        new QueryCollection(
            description: 'Get all posts of subscribed blogs',
            security: 'is_granted("ROLE_USER")',
            name: 'subscribed',
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['blog.id' => 'exact'])]
#[ORM\HasLifecycleCallbacks]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('post:read')]
    /**
     * @phpstan-ignore-next-line Doctrine sets the ID at runtime
     */
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['post:read', 'post:write'])]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[Groups(['post:write', 'post:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[Groups(['post:read', 'post:write'])]
    #[MaxDepth(1)]
    private ?Blog $blog = null;

    #[ORM\Column(length: 255)]
    #[Groups(['post:read', 'post:write'])]
    private ?string $title = null;

    #[ORM\Column]
    #[Groups(['post:read'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups(['post:read'])]
    private ?\DateTimeImmutable $updated_at = null;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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
