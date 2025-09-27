<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\GraphQL;

use GraphQL\Type\Definition\ResolveInfo;

abstract readonly class AbstractGenerateResolver
{
    public function __construct(
        private string $openAIKey,
    ) {
    }

    public function __invoke(mixed $root, array $args, ?array $context = null, ?ResolveInfo $info = null): object
    {
        $this->validateRequest($args);

        $client = \OpenAI::client($this->openAIKey);
        $response = $client->responses()->create([
            'model' => 'gpt-5-nano',
            'input' => $this->getPrompt($args),
        ]);
        $response = json_decode($response->outputText, true);

        return $this->prepareResponse($response);
    }

    abstract protected function validateRequest(array $args): void;

    abstract protected function getSchema(): string;

    abstract protected function getPrompt(array $args): string;

    abstract protected function prepareResponse(array $response): object;
}
