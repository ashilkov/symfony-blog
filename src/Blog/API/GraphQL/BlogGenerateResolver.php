<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\GraphQL;

use App\Blog\API\DTO\Response\Blog\GeneratedBlog;

readonly class BlogGenerateResolver extends AbstractGenerateResolver
{
    protected function validateRequest(array $args): void
    {
    }

    protected function getSchema(): string
    {
        return '{"name":generated_title, "description": generated_content}';
    }

    protected function getPrompt(array $args): string
    {
        $name = $args['args']['name'] ?? '';
        $description = $args['args']['description'] ?? '';

        return "Generate a general content for a new blog that will have posts later. Return result as JSON in the following form: {$this->getSchema()}. Name: {$name}. Description: {$description} (use html for better formatting, keep it short).";
    }

    protected function prepareResponse(array $response): object
    {
        return new GeneratedBlog(
            name: $response['name'] ?? null,
            description: $response['description'] ?? null,
        );
    }
}
