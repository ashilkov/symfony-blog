<?php

namespace App\Tests\Blog\Application\Handler\Blog;

use App\Blog\Application\Command\Blog\DeleteCommand;
use App\Blog\Application\Handler\Blog\DeleteHandler;
use App\Blog\Domain\Model\Blog;
use App\Blog\Domain\Repository\BlogRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DeleteHandlerTest extends TestCase
{
    private $blogs;
    private $handler;

    protected function setUp(): void
    {
        $this->blogs = $this->createMock(BlogRepositoryInterface::class);
        $this->handler = new DeleteHandler($this->blogs);
    }

    public function testDeleteBlogSuccessfully(): void
    {
        $blogId = 456;
        $command = new DeleteCommand($blogId);

        $blog = $this->createMock(Blog::class);

        $this->blogs->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $blogId])
            ->willReturn($blog);

        $this->blogs->expects($this->once())
            ->method('remove')
            ->with($blog, true);

        ($this->handler)($command);
    }

    public function testDeleteNonExistentBlogThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Blog not found.');

        $blogId = 456;
        $command = new DeleteCommand($blogId);

        $this->blogs->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $blogId])
            ->willReturn(null);

        ($this->handler)($command);
    }
}
