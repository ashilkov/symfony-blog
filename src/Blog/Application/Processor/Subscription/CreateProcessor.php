<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Processor\Subscription;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Blog\API\Hydrator\SubscriptionHydrator;
use App\Blog\API\Resource\Subscription;
use App\Blog\Application\Command\Subscription\CreateCommand;
use App\Blog\Application\Handler\Subscription\CreateHandler;
use Symfony\Bundle\SecurityBundle\Security;

readonly class CreateProcessor implements ProcessorInterface
{
    public function __construct(
        private CreateHandler $handler,
        private SubscriptionHydrator $hydrator,
        private Security $security,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Subscription
    {
        $user = $this->security->getUser();
        if (null === $user) {
            throw new AccessDeniedException('You are not allowed to create a blog.');
        }

        $command = new CreateCommand(
            blogId: $data->blogId ?? null,
            subscriberId: $user->getId() ?? null,
        );

        $subscription = ($this->handler)($command);

        return $this->hydrator->hydrate($subscription);
    }
}
