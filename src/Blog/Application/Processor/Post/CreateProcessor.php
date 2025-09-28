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
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Blog\API\Hydrator\PostHydrator;
use App\Blog\API\Resource\Post as PostResource;
use App\Blog\Application\Command\Post\CreateCommand;
use App\Blog\Application\Handler\Post\CreateHandler;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @implements ProcessorInterface<PostResource, PostResource>
 */
readonly class CreateProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private CreateHandler $handler,
        private PostHydrator $postHydrator,
    ) {
    }

    /**
     * @param PostResource|object $data
     *
     * @return PostResource
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $user = $this->security->getUser();
        if (null === $user) {
            throw new AccessDeniedException('You are not allowed to create a post.');
        }

        $command = new CreateCommand(
            title: $data->title,
            content: $data->content,
            blogId: $data->blogId,
            userId: (int) $user->getId() ?? null,
        );
        $post = ($this->handler)($command);

        return $this->postHydrator->hydrate($post);
    }
}
