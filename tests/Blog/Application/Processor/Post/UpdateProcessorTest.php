<?php

namespace App\Tests\Blog\Application\Processor\Post;

use App\Blog\API\Hydrator\BlogHydrator;
use App\Blog\API\Hydrator\PostHydrator;
use App\Blog\API\Resource\Post as PostResource;
use App\Blog\Application\Command\Post\UpdateCommand;
use App\Blog\Application\Handler\Post\UpdateHandler;
use App\Blog\Application\Processor\Post\UpdateProcessor;
use App\Blog\Domain\Model\Blog as DomainBlog;
use App\Blog\Domain\Model\Post as DomainPost;
use ApiPlatform\Metadata\Operation;
use PHPUnit\Framework\TestCase;

class UpdateProcessorTest extends TestCase
{
    private $handler;
    private $blogHydrator;
    private $postHydrator;
    private $processor;

    protected function setUp(): void
    {
        $this->handler = $this->createMock(UpdateHandler::class);
        $this->blogHydrator = $this->createMock(BlogHydrator::class);
        $this->postHydrator = $this->createMock(PostHydrator::class);
        $this->processor = new UpdateProcessor(
            $this->handler,
            $this->blogHydrator,
            $this->postHydrator
        );
    }

    public function testProcessWithPostIdInUriVariables(): void
    {
        $data = new PostResource();
        $data->title = 'Updated Title';
        $data->content = 'Updated Content';
        
        $operation = $this->createMock(Operation::class);
        $uriVariables = ['post_id' => 123];
        $context = [];

        $domainPost = $this->createMock(DomainPost::class);
        $postResource = new PostResource();

        $this->handler->expects($this->once())
            ->method('__invoke')
            ->with($this->callback(function (UpdateCommand $command) {
                return $command->id === 123 &&
                       $command->title === 'Updated Title' &&
                       $command->content === 'Updated Content';
            }))
            ->willReturn($domainPost);

        $this->postHydrator->expects($this->once())
            ->method('hydrate')
            ->with($domainPost)
            ->willReturn($postResource);

        $result = $this->processor->process($data, $operation, $uriVariables, $context);
        $this->assertSame($postResource, $result);
    }

    public function testProcessWithIdInUriVariables(): void
    {
        $data = new PostResource();
        $data->title = 'Updated Title';
        $data->content = 'Updated Content';
        
        $operation = $this->createMock(Operation::class);
        $uriVariables = ['id' => 456];
        $context = [];

        $domainPost = $this->createMock(DomainPost::class);
        $postResource = new PostResource();

        $this->handler->expects($this->once())
            ->method('__invoke')
            ->with($this->callback(function (UpdateCommand $command) {
                return $command->id === 456;
            }))
            ->willReturn($domainPost);

        $this->postHydrator->expects($this->once())
            ->method('hydrate')
            ->with($domainPost)
            ->willReturn($postResource);

        $result = $this->processor->process($data, $operation, $uriVariables, $context);
        $this->assertSame($postResource, $result);
    }

    public function testProcessWithIdInData(): void
    {
        $data = new PostResource();
        $data->id = 789;
        $data->title = 'Updated Title';
        $data->content = 'Updated Content';
        
        $operation = $this->createMock(Operation::class);
        $uriVariables = [];
        $context = [];

        $domainPost = $this->createMock(DomainPost::class);
        $postResource = new PostResource();

        $this->handler->expects($this->once())
            ->method('__invoke')
            ->with($this->callback(function (UpdateCommand $command) {
                return $command->id === 789;
            }))
            ->willReturn($domainPost);

        $this->postHydrator->expects($this->once())
            ->method('hydrate')
            ->with($domainPost)
            ->willReturn($postResource);

        $result = $this->processor->process($data, $operation, $uriVariables, $context);
        $this->assertSame($postResource, $result);
    }

    public function testProcessThrowsExceptionWhenIdIsMissing(): void
    {
        $data = new PostResource();
        $operation = $this->createMock(Operation::class);
        $uriVariables = [];
        $context = [];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Post ID is required for update.');

        $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
