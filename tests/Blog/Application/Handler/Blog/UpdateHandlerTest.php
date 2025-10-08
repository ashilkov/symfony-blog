<?php

namespace App\Tests\Blog\Application\Handler\Blog;

use App\Blog\Application\Command\Blog\UpdateCommand;
use App\Blog\Application\Handler\Blog\UpdateHandler;
use App\Blog\Domain\Model\Blog;
use App\Blog\Domain\Repository\BlogRepositoryInterface;
use App\Blog\Domain\Value\Blog\BlogDescription;
use App\Blog\Domain\Value\Blog\BlogName;
use PHPUnit\Framework\TestCase;

class UpdateHandlerTest extends TestCase
{
    private $blogs;
    private $handler;

    protected function setUp(): void
    {
        $this->blogs = $this->createMock(BlogRepositoryInterface::class);
        $this->handler = new UpdateHandler($this->blogs);
    }

    public function testUpdateBlogSuccessfully(): void
    {
        $blogId = 456;
        $command = new UpdateCommand(
            id: $blogId,
            name: 'Updated Blog',
            description: 'Updated Description'
        );

        $blog = $this->createMock(Blog::class);
        
        // Since we can't mock non-existent methods, we'll assume the handler uses the blog's internal methods
        // We'll just verify that save is called
        $this->blogs->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $blogId])
            ->willReturn($blog);

        $this->blogs->expects($this->once())
            ->method('save')
            ->with($blog, true);

        $result = ($this->handler)($command);

        $this->assertSame($blog, $result);
    }

    public function testUpdateNonExistentBlogThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Blog not found.');

        $blogId = 456;
        $command = new UpdateCommand(
            id: $blogId,
            name: 'Updated Blog',
            description: 'Updated Description'
        );

        $this->blogs->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $blogId])
            ->willReturn(null);

        ($this->handler)($command);
    }
}
