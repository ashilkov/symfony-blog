<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\API\GraphQL;

use App\Blog\API\DTO\Response\GeneratedPost;
use App\Blog\Domain\Repository\BlogRepositoryInterface;

readonly class PostGenerateResolver extends AbstractGenerateResolver
{
    public function __construct(
        private BlogRepositoryInterface $blogRepository,
        string $openAIKey,
    ) {
        parent::__construct($openAIKey);
    }

    protected function validateRequest(array $args): void
    {
        if (!isset($args['args']['blogId'])) {
            throw new \InvalidArgumentException('Missing required argument `blogId`');
        }
    }

    protected function getSchema(): string
    {
        return '{"title":generated_title, "content": generated_content}';
    }

    protected function getPrompt(array $args): string
    {
        $title = $args['args']['title'] ?? '';
        $content = $args['args']['content'] ?? '';
        $blog = $this->blogRepository->findOneBy(['id' => $args['args']['blogId']]);

        return "Generate a content for a moderate size blog post. Return result as JSON in the following form: {$this->getSchema()}.
        Title: {$title}. Content: {$content} (use html for better formatting).
        Blog name: {$blog->getName()}. Blog description: {$blog->getDescription()}.
        Feel free to update both the title and the content. Post should have a specific topic and shouldn't mention the blog name or description.";
    }

    protected function prepareResponse(array $response): object
    {
        return new GeneratedPost(
            title: $response['title'] ?? null,
            content: $response['content'] ?? null,
        );
    }
}
