<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Domain\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GraphQl\DeleteMutation;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use App\Blog\API\DTO\BlogResponse;
use App\Blog\API\GraphQL\BlogGenerateResolver;
use App\Blog\Infrastructure\Repository\BlogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

// todo: move all requests and responses to DTO
#[ApiResource(
    operations: [],
    normalizationContext: ['groups' => ['blog:read'], 'iri_only' => false, 'enable_max_depth' => true],
    denormalizationContext: ['groups' => ['blog:write']],
    graphQlOperations: [
        new Query(),
        new QueryCollection(),
        new Mutation(name: 'create'),
        new Mutation(name: 'update'),
        new DeleteMutation(name: 'delete'),
        new Query(
            resolver: BlogGenerateResolver::class,
            args: ['name' => ['type' => 'String'], 'description' => ['type' => 'String']],
            description: 'Generate blog information',
            security: 'is_granted("ROLE_USER")',
            output: BlogResponse::class,
            name: 'generate',
        ),
    ]
)]
#[ORM\Entity(repositoryClass: BlogRepository::class)]
class Blog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('blog:read')]
    /**
     * @phpstan-ignore-next-line Doctrine sets the ID at runtime
     */
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['blog:read', 'blog:write', 'post:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['blog:read', 'blog:write'])]
    private ?string $description = null;

    /**
     * @var Collection <int, BlogUser>
     */
    #[ORM\OneToMany(targetEntity: BlogUser::class, mappedBy: 'blog')]
    #[Groups(['blog:read', 'post:read'])]
    #[MaxDepth(1)]
    private Collection $blogUsers;

    /**
     * @var Collection<int, Post>
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'blog')]
    #[Groups(['blog:read'])]
    #[MaxDepth(1)]
    private Collection $posts;

    /**
     * @var Collection<int, Subscription>
     */
    #[ORM\OneToMany(targetEntity: Subscription::class, mappedBy: 'blog')]
    private Collection $subscriptions;

    public function __construct()
    {
        $this->blogUsers = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
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

    /**
     * @return Collection<int, BlogUser>
     */
    public function getBlogUsers(): Collection
    {
        return $this->blogUsers;
    }

    public function addBlogUser(BlogUser $blogUser): self
    {
        if (!$this->blogUsers->contains($blogUser)) {
            $this->blogUsers->add($blogUser);
        }

        return $this;
    }

    public function removeBlogUser(BlogUser $blogUser): self
    {
        $this->blogUsers->removeElement($blogUser);

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setBlog($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getBlog() === $this) {
                $post->setBlog(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Subscription>
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): static
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions->add($subscription);
            $subscription->setBlog($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): static
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getBlog() === $this) {
                $subscription->setBlog(null);
            }
        }

        return $this;
    }
}
