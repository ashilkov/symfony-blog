<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Tests\Blog\Application\Processor\Blog;

use App\Blog\API\Resource\Blog;
use App\Blog\Application\Command\Blog\UpdateCommand;
use App\Blog\Application\Handler\Blog\UpdateHandler;
use App\Blog\Application\Processor\Blog\UpdateProcessor;
use App\Blog\API\Hydrator\BlogHydrator;
use App\Blog\API\Hydrator\PostHydrator;
use App\Blog\API\Hydrator\BlogUserHydrator;
use PHPUnit\Framework\TestCase;
use ApiPlatform\Metadata\Operation;

class UpdateProcessorTest extends TestCase
{
    private $handler;
    private $blogHydrator;
    private $postHydrator;
    private $blogUserHydrator;
    private $processor;

    protected function setUp(): void
    {
        $this->handler = $this->createMock(UpdateHandler::class);
        $this->blogHydrator = $this->createMock(BlogHydrator::class);
        $this->postHydrator = $this->createMock(PostHydrator::class);
        $this->blogUserHydrator = $this->createMock(BlogUserHydrator::class);
        $this->processor = new UpdateProcessor(
            $this->handler,
            $this->blogHydrator,
            $this->postHydrator,
            $this->blogUserHydrator
        );
    }

    public function testProcessWithBlogIdInUriVariables(): void
    {
        $operation = $this->createMock(Operation::class);
        $uriVariables = ['blog_id' => 123];
        $context = [];
        
        $data = new Blog();
        $data->name = 'Updated Blog';
        $data->description = 'Updated Description';

        // Mock the handler to return a domain blog
        $domainBlog = $this->createMock(\App\Blog\Domain\Model\Blog::class);
        $domainBlog->expects($this->once())
            ->method('getPosts')
            ->willReturn(new \ArrayIterator([]));
        $domainBlog->expects($this->once())
            ->method('getBlogUsers')
            ->willReturn(new \ArrayIterator([]));
        
        $this->handler->expects($this->once())
            ->method('__invoke')
            ->with($this->callback(function (UpdateCommand $command) {
                return $command->id === 123 
                    && $command->name === 'Updated Blog' 
                    && $command->description === 'Updated Description';
            }))
            ->willReturn($domainBlog);

        // Mock hydrators
        $expectedResource = new Blog();
        $expectedResource->id = 123;
        $expectedResource->name = 'Updated Blog';
        $expectedResource->description = 'Updated Description';
        $expectedResource->posts = [];
        $expectedResource->blogUsers = [];
        $this->blogHydrator->expects($this->once())
            ->method('hydrate')
            ->with($domainBlog)
            ->willReturn($expectedResource);

        $result = $this->processor->process($data, $operation, $uriVariables, $context);

        $this->assertInstanceOf(Blog::class, $result);
        $this->assertEquals(123, $result->id);
        $this->assertEquals('Updated Blog', $result->name);
        $this->assertEquals('Updated Description', $result->description);
    }

    public function testProcessWithIdInUriVariables(): void
    {
        $operation = $this->createMock(Operation::class);
        $uriVariables = ['id' => 456];
        $context = [];
        
        $data = new Blog();
        $data->name = 'Updated Blog 2';
        $data->description = 'Updated Description 2';

        // Mock the handler to return a domain blog
        $domainBlog = $this->createMock(\App\Blog\Domain\Model\Blog::class);
        $domainBlog->expects($this->once())
            ->method('getPosts')
            ->willReturn(new \ArrayIterator([]));
        $domainBlog->expects($this->once())
            ->method('getBlogUsers')
            ->willReturn(new \ArrayIterator([]));
        
        $this->handler->expects($this->once())
            ->method('__invoke')
            ->with($this->callback(function (UpdateCommand $command) {
                return $command->id === 456 
                    && $command->name === 'Updated Blog 2' 
                    && $command->description === 'Updated Description 2';
            }))
            ->willReturn($domainBlog);

        // Mock hydrators
        $expectedResource = new Blog();
        $expectedResource->id = 456;
        $expectedResource->name = 'Updated Blog 2';
        $expectedResource->description = 'Updated Description 2';
        $expectedResource->posts = [];
        $expectedResource->blogUsers = [];
        $this->blogHydrator->expects($this->once())
            ->method('hydrate')
            ->with($domainBlog)
            ->willReturn($expectedResource);

        $result = $this->processor->process($data, $operation, $uriVariables, $context);

        $this->assertInstanceOf(Blog::class, $result);
        $this->assertEquals(456, $result->id);
        $this->assertEquals('Updated Blog 2', $result->name);
        $this->assertEquals('Updated Description 2', $result->description);
    }

    public function testProcessWithDataId(): void
    {
        $operation = $this->createMock(Operation::class);
        $uriVariables = [];
        $context = [];
        
        $data = new Blog();
        $data->id = 789;
        $data->name = 'Updated Blog 3';
        $data->description = 'Updated Description 3';

        // Mock the handler to return a domain blog
        $domainBlog = $this->createMock(\App\Blog\Domain\Model\Blog::class);
        $domainBlog->expects($this->once())
            ->method('getPosts')
            ->willReturn(new \ArrayIterator([]));
        $domainBlog->expects($this->once())
            ->method('getBlogUsers')
            ->willReturn(new \ArrayIterator([]));
        
        $this->handler->expects($this->once())
            ->method('__invoke')
            ->with($this->callback(function (UpdateCommand $command) {
                return $command->id === 789 
                    && $command->name === 'Updated Blog 3' 
                    && $command->description === 'Updated Description 3';
            }))
            ->willReturn($domainBlog);

        // Mock hydrators
        $expectedResource = new Blog();
        $expectedResource->id = 789;
        $expectedResource->name = 'Updated Blog 3';
        $expectedResource->description = 'Updated Description 3';
        $expectedResource->posts = [];
        $expectedResource->blogUsers = [];
        $this->blogHydrator->expects($this->once())
            ->method('hydrate')
            ->with($domainBlog)
            ->willReturn($expectedResource);

        $result = $this->processor->process($data, $operation, $uriVariables, $context);

        $this->assertInstanceOf(Blog::class, $result);
        $this->assertEquals(789, $result->id);
        $this->assertEquals('Updated Blog 3', $result->name);
        $this->assertEquals('Updated Description 3', $result->description);
    }

    public function testProcessWithoutIdThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Blog ID is required for update.');

        $operation = $this->createMock(Operation::class);
        $uriVariables = [];
        $context = [];
        
        $data = new Blog();
        $data->name = 'Test Blog';
        $data->description = 'Test Description';

        $this->processor->process($data, $operation, $uriVariables, $context);
    }

    public function testProcessWithNullDataThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Blog data is required for update.');

        $operation = $this->createMock(Operation::class);
        $uriVariables = ['blog_id' => 123];
        $context = [];

        $this->processor->process(null, $operation, $uriVariables, $context);
    }
}
