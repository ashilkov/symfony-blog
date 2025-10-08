<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Tests\Blog\Application\Processor\Post;

use App\Blog\API\Resource\Post;
use App\Blog\Application\Command\Post\DeleteCommand;
use App\Blog\Application\Handler\Post\DeleteHandler;
use App\Blog\Application\Processor\Post\DeleteProcessor;
use PHPUnit\Framework\TestCase;
use ApiPlatform\Metadata\Operation;

class DeleteProcessorTest extends TestCase
{
    private $handler;
    private $processor;

    protected function setUp(): void
    {
        $this->handler = $this->createMock(DeleteHandler::class);
        $this->processor = new DeleteProcessor($this->handler);
    }

    public function testProcessWithPostIdInUriVariables(): void
    {
        $operation = $this->createMock(Operation::class);
        $uriVariables = ['post_id' => 123];
        $context = [];

        $this->handler->expects($this->once())
            ->method('__invoke')
            ->with($this->callback(function (DeleteCommand $command) {
                return $command->id === 123;
            }));

        $result = $this->processor->process(null, $operation, $uriVariables, $context);

        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals(123, $result->id);
    }

    public function testProcessWithIdInUriVariables(): void
    {
        $operation = $this->createMock(Operation::class);
        $uriVariables = ['id' => 456];
        $context = [];

        $this->handler->expects($this->once())
            ->method('__invoke')
            ->with($this->callback(function (DeleteCommand $command) {
                return $command->id === 456;
            }));

        $result = $this->processor->process(null, $operation, $uriVariables, $context);

        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals(456, $result->id);
    }

    public function testProcessWithDataId(): void
    {
        $operation = $this->createMock(Operation::class);
        $uriVariables = [];
        $context = [];
        
        $data = new \stdClass();
        $data->id = 789;

        $this->handler->expects($this->once())
            ->method('__invoke')
            ->with($this->callback(function (DeleteCommand $command) {
                return $command->id === 789;
            }));

        $result = $this->processor->process($data, $operation, $uriVariables, $context);

        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals(789, $result->id);
    }

    public function testProcessWithoutIdThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Post ID is required for delete.');

        $operation = $this->createMock(Operation::class);
        $uriVariables = [];
        $context = [];

        $this->processor->process(null, $operation, $uriVariables, $context);
    }
}
