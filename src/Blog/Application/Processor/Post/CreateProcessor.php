<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Processor\Post;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Blog\API\Hydrator\PostHydrator;
use App\Blog\API\Resource\Post as PostResource;
use App\Blog\Domain\Model\Post;
use App\Blog\Domain\Repository\BlogRepositoryInterface;
use App\Blog\Domain\Repository\PostRepositoryInterface;
use App\User\Domain\Model\User;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @implements ProcessorInterface<PostResource, PostResource>
 */
readonly class CreateProcessor implements ProcessorInterface
{
    /**
     * @param Security $security
     * @param PostRepositoryInterface $postRepository
     * @param BlogRepositoryInterface $blogRepository
     * @param PostHydrator $postHydrator
     */
    public function __construct(
        private Security                $security,
        private PostRepositoryInterface $postRepository,
        private BlogRepositoryInterface $blogRepository,
        private PostHydrator            $postHydrator
    )
    {
    }

    /**
     * @param PostResource|object $data
     *
     * @return PostResource
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $post = new Post();
        if (isset($data->title)) {
            $post->setTitle($data->title);
        }
        if (isset($data->content)) {
            $post->setContent($data->content);
        }
        if (isset($data->blog)) {
            $blog = $this->blogRepository->findOneBy(['id' => $data->blog->id]);
            $post->setBlog($blog);
        }

        $user = $this->security->getUser();
        if ($user instanceof User) {
            $post->setUser($user);
        }

        $this->postRepository->save($post, true);

        return $this->postHydrator->hydrate($post);
    }
}
