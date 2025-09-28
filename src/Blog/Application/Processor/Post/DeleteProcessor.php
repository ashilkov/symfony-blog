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
use App\Blog\API\Resource\Post;
use App\Blog\Application\Command\Post\DeleteCommand;
use App\Blog\Application\Handler\Post\DeleteHandler;

readonly class DeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private DeleteHandler $handler,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Post
    {
        $id = $uriVariables['post_id'] ?? $uriVariables['id'] ?? $data->id ?? null;
        if (null === $id) {
            throw new \InvalidArgumentException('Post ID is required for delete.');
        }

        ($this->handler)(new DeleteCommand((int) $id));

        return new Post(id: (int) $id);
    }
}
