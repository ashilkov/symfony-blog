<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Application\Processor\Blog;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Blog\API\Resource\Blog;
use App\Blog\Application\Command\Blog\DeleteCommand;
use App\Blog\Application\Handler\Blog\DeleteHandler;

readonly class DeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private DeleteHandler $handler,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Blog
    {
        $id = $uriVariables['blog_id'] ?? $uriVariables['id'] ?? $data->id ?? null;
        if (null === $id) {
            throw new \InvalidArgumentException('Blog ID is required for delete.');
        }

        ($this->handler)(new DeleteCommand((int) $id));

        return new Blog(id: (int) $id);
    }
}
