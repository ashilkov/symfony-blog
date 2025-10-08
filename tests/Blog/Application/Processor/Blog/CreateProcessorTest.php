<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Tests\Blog\Application\Processor\Blog;

use App\Blog\API\Resource\Blog;
use App\Blog\Application\Command\Blog\CreateCommand;
use App\Blog\Application\Handler\Blog\CreateHandler;
use App\Blog\Application\Processor\Blog\CreateProcessor;
use App\Blog\Application\CurrentUserProviderInterface;
use App\Blog\API\Hydrator\BlogHydrator;
use PHPUnit\Framework\TestCase;
use ApiPlatform\Metadata\Operation;

class CreateProcessorTest extends TestCase
{
    private $handler;
    private $hydrator;
    private $userProvider;
    private $processor;

    protected function setUp(): void
    {
        $this->handler = $this->createMock(CreateHandler::class);
        $this->hydrator = $this->createMock(BlogHydrator::class);
        $this->userProvider = $this->createMock(CurrentUserProviderInterface::class);
        $this->processor = new CreateProcessor($this->handler, $this->hydrator, $this->userProvider);
    }

    public function testProcessWithValidData(): void
    {
        $operation = $this->createMock(Operation::class);
        $uriVariables = [];
        $context = [];
        
        $data = new Blog();
        $data->name = 'Test Blog';
        $data->description = 'Test Description';

        // Mock user provider to return a user ID
        $this->userProvider->expects($this->once())
            ->method('getUserId')
            ->willReturn(123);

        // Mock the handler to return a domain blog
        $domainBlog = $this->createMock(\App\Blog\Domain\Model\Blog::class);
        $this->handler->expects($this->once())
            ->method('__invoke')
            ->with($this->callback(function (CreateCommand $command) {
                return $command->name === 'Test Blog' 
                    && $command->description === 'Test Description'
                    && $command->userId === 123;
            }))
            ->willReturn($domainBlog);

        // Mock hydrator to return a resource
        $expectedResource = new Blog();
        $expectedResource->name = 'Test Blog';
        $expectedResource->description = 'Test Description';
        $this->hydrator->expects($this->once())
            ->method('hydrate')
            ->with($domainBlog)
            ->willReturn($expectedResource);

        $result = $this->processor->process($data, $operation, $uriVariables, $context);

        $this->assertInstanceOf(Blog::class, $result);
        $this->assertEquals('Test Blog', $result->name);
        $this->assertEquals('Test Description', $result->description);
    }

    public function testProcessWithNullDataThrowsException(): void
    {
        $this->expectException(\ApiPlatform\Symfony\Security\Exception\AccessDeniedException::class);
        $this->expectExceptionMessage('You are not allowed to create a blog.');

        $operation = $this->createMock(Operation::class);
        $uriVariables = [];
        $context = [];

        $this->processor->process(null, $operation, $uriVariables, $context);
    }

    public function testProcessWithoutUserThrowsAccessDeniedException(): void
    {
        $this->expectException(\ApiPlatform\Symfony\Security\Exception\AccessDeniedException::class);
        $this->expectExceptionMessage('You are not allowed to create a blog.');

        $operation = $this->createMock(Operation::class);
        $uriVariables = [];
        $context = [];
        
        $data = new Blog();
        $data->name = 'Test Blog';
        $data->description = 'Test Description';

        // Mock user provider to return null (no user)
        $this->userProvider->expects($this->once())
            ->method('getUserId')
            ->willReturn(null);

        $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
