<?php

namespace App\Tests\Blog\Application\Processor\Post;

use App\Blog\API\Hydrator\PostHydrator;
use App\Blog\API\Resource\Post as PostResource;
use App\Blog\Application\Command\Post\CreateCommand;
use App\Blog\Application\CurrentUserProviderInterface;
use App\Blog\Application\Handler\Post\CreateHandler;
use App\Blog\Application\Processor\Post\CreateProcessor;
use App\Blog\Domain\Model\Post as DomainPost;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use PHPUnit\Framework\TestCase;

class CreateProcessorTest extends TestCase
{
    private $handler;
    private $postHydrator;
    private $userProvider;
    private $processor;

    protected function setUp(): void
    {
        $this->handler = $this->createMock(CreateHandler::class);
        $this->postHydrator = $this->createMock(PostHydrator::class);
        $this->userProvider = $this->createMock(CurrentUserProviderInterface::class);
        $this->processor = new CreateProcessor(
            $this->handler,
            $this->postHydrator,
            $this->userProvider
        );
    }

    public function testProcessSuccessfully(): void
    {
        $data = new PostResource();
        $data->title = 'Test Title';
        $data->content = 'Test Content';
        // Create a mock Blog resource
        $blogResource = $this->createMock(\App\Blog\API\Resource\Blog::class);
        $blogResource->id = 1;
        $data->blog = $blogResource;
        
        $operation = $this->createMock(Operation::class);
        $uriVariables = [];
        $context = [];

        $this->userProvider->expects($this->once())
            ->method('getUserId')
            ->willReturn(123);

        $domainPost = $this->createMock(DomainPost::class);
        $expectedResource = new PostResource();

        $this->handler->expects($this->once())
            ->method('__invoke')
            ->with($this->callback(function (CreateCommand $command) {
                return $command->title === 'Test Title' &&
                       $command->content === 'Test Content' &&
                       $command->blogId === 1 &&
                       $command->userId === 123;
            }))
            ->willReturn($domainPost);

        $this->postHydrator->expects($this->once())
            ->method('hydrate')
            ->with($domainPost)
            ->willReturn($expectedResource);

        $result = $this->processor->process($data, $operation, $uriVariables, $context);

        $this->assertSame($expectedResource, $result);
    }

    public function testProcessThrowsAccessDeniedWhenUserIsNull(): void
    {
        $data = new PostResource();
        $operation = $this->createMock(Operation::class);
        $uriVariables = [];
        $context = [];

        $this->userProvider->expects($this->once())
            ->method('getUserId')
            ->willReturn(null);

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('You are not allowed to create a post.');

        $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
